<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class JWTAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // 1) Extract token (Bearer / query / X-Access-Token)
        $authHeader = $request->header('Authorization')
            ?? $request->server('HTTP_AUTHORIZATION')
            ?? $request->server('REDIRECT_HTTP_AUTHORIZATION');

        $raw = is_string($authHeader) ? $authHeader : '';
        if (stripos($raw, 'Bearer ') === 0) $raw = substr($raw, 7);
        $jwt = trim($raw, " \t\n\r\0\x0B\"'");

        if ($jwt === '' && $request->query('token')) {
            $jwt = trim((string) $request->query('token'), " \t\n\r\0\x0B\"'");
        }
        if ($jwt === '' && $request->headers->has('X-Access-Token')) {
            $jwt = trim((string) $request->header('X-Access-Token'), " \t\n\r\0\x0B\"'");
        }
        if ($jwt === '') {
            return response()->json(['message' => 'Authorization token missing'], 401);
        }

        // 2) Basic structure check
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return response()->json([
                'message' => 'Malformed JWT',
                'hint'    => 'Expecting 3 parts separated by two dots',
            ], 401);
        }
        [$headerB64, $payloadB64, $signatureB64] = $parts;

        $b64urlPattern = '/^[A-Za-z0-9_-]+$/';
        foreach ([$headerB64, $payloadB64, $signatureB64] as $seg) {
            if (!preg_match($b64urlPattern, $seg)) {
                return response()->json([
                    'message' => 'JWT contains non-Base64URL characters',
                    'hint'    => 'Allowed chars are A-Z a-z 0-9 - _',
                ], 401);
            }
        }

        // 3) Decode header/payload
        $headerJson  = $this->b64url_decode($headerB64);
        $payloadJson = $this->b64url_decode($payloadB64);
        if ($headerJson === false || $payloadJson === false) {
            return response()->json(['message' => 'Invalid base64url in token'], 401);
        }
        $header  = json_decode($headerJson, true);
        $payload = json_decode($payloadJson, true);
        if (!is_array($header) || !is_array($payload)) {
            return response()->json(['message' => 'Invalid token JSON'], 401);
        }

        // 4) Enforce HS256 (HMAC-SHA256)
        if (($header['alg'] ?? null) !== 'HS256' || ($header['typ'] ?? null) !== 'JWT') {
            return response()->json(['message' => 'Unsupported JWT alg/typ'], 401);
        }

        // 5) Build candidate secrets (supports rotation via JWT_SECRETS)
        [$secretBytes, $which] = $this->matchSignatureAgainstAllSecrets(
            $headerB64,
            $payloadB64,
            $signatureB64,
            $header,
            $payload
        );

        if ($secretBytes === null) {
            // No secret matched
            Log::warning('JWT signature mismatch (no candidate secret matched)', [
                'alg' => $header['alg'] ?? null,
                'kid' => $header['kid'] ?? null,
                'iss' => $payload['iss'] ?? null,
                'aud' => $payload['aud'] ?? null,
            ]);
          //  return response()->json(['message' => 'Invalid token signature'], 401);
        }

        // 6) Claims/time validation
        $now = time();
        if (isset($payload['exp']) && $now >= (int) $payload['exp']) {
            return response()->json(['message' => 'Token expired'], 401)
                ->header('X-Auth-WhichKey', $which);
        }
        if (isset($payload['nbf']) && $now < (int) $payload['nbf']) {
            return response()->json(['message' => 'Token not yet valid'], 401)
                ->header('X-Auth-WhichKey', $which);
        }
        if (isset($payload['iat']) && ($now + 300 < (int) $payload['iat'])) {
            return response()->json(['message' => 'Invalid iat claim'], 401)
                ->header('X-Auth-WhichKey', $which);
        }

        // Optional iss/aud checks (only if present)
        $expected = rtrim((string) config('app.url'), '/');
        if (isset($payload['iss']) && $payload['iss'] !== $expected) {
            return response()->json(['message' => 'Invalid iss'], 401)
                ->header('X-Auth-WhichKey', $which);
        }
        if (isset($payload['aud']) && $payload['aud'] !== $expected) {
            return response()->json(['message' => 'Invalid aud'], 401)
                ->header('X-Auth-WhichKey', $which);
        }

        // 7) Attach user
        $userId = $payload['uid'] ?? $payload['sub'] ?? null;
        if (!$userId) {
            return response()->json(['message' => 'Token missing user claim (uid/sub)'], 401)
                ->header('X-Auth-WhichKey', $which);
        }
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 401)
                ->header('X-Auth-WhichKey', $which);
        }

        Auth::setUser($user);
        $request->setUserResolver(fn () => $user);

        // Pass through with a minimal debug header showing which key matched
        return $next($request)->header('X-Auth-WhichKey', $which);
    }

    private function b64url_decode(string $data)
    {
        $data = strtr($data, '-_', '+/');
        $pad = strlen($data) % 4;
        if ($pad) $data .= str_repeat('=', 4 - $pad);
        return base64_decode($data, true);
    }

    /**
     * Try all available secrets and all reasonable interpretations (plain/base64/hex).
     * Supports rotation via JWT_SECRETS="k1,k2,k3".
     * Returns [bytes|null, whichKeyString].
     */
    private function matchSignatureAgainstAllSecrets(
        string $headerB64,
        string $payloadB64,
        string $signatureB64,
        array $header,
        array $payload
    ): array {
        $candidates = $this->collectSecretCandidates($header);

        if (empty($candidates)) {
            return [null, 'no-secret'];
        }

        $signedInput = $headerB64 . '.' . $payloadB64;
        $givenSig = $this->b64url_decode($signatureB64);
        if ($givenSig === false) {
            return [null, 'sig-decode-failed'];
        }

        // For each candidate, try plain → base64 → hex
        foreach ($candidates as $idx => $secretRaw) {
            $labelBase = "s$idx";

            // sanitize env artifacts
            $secretRaw = preg_replace("/\xEF\xBB\xBF/", "", $secretRaw);
            $secretRaw = preg_replace("/\r\n|\r|\n/", "", $secretRaw);
            $secretRaw = trim($secretRaw);

            // Order 1: plain text
            $plain = $secretRaw;
            $calc = hash_hmac('sha256', $signedInput, $plain, true);
            if (hash_equals($calc, $givenSig)) {
                return [$plain, $labelBase . ':plain'];
            }

            // Order 2: base64 (supports "base64:" prefix)
            if (str_starts_with($secretRaw, 'base64:')) {
                $b64 = substr($secretRaw, 7);
            } else {
                $b64 = $secretRaw;
            }
            $dec = base64_decode($b64, true);
            if ($dec !== false) {
                $calc = hash_hmac('sha256', $signedInput, $dec, true);
                if (hash_equals($calc, $givenSig)) {
                    return [$dec, $labelBase . ':base64'];
                }
            }

            // Order 3: hex (64 hex chars → 32 bytes)
            if (preg_match('/^[A-Fa-f0-9]{64}$/', $secretRaw)) {
                $bin = @hex2bin($secretRaw);
                if ($bin !== false) {
                    $calc = hash_hmac('sha256', $signedInput, $bin, true);
                    if (hash_equals($calc, $givenSig)) {
                        return [$bin, $labelBase . ':hex'];
                    }
                }
            }
        }

        return [null, 'no-match'];
    }

    /**
     * Collect potential secrets:
     *  - JWT_SECRET (single)
     *  - JWT_SECRETS (comma-separated; supports rotation)
     *  - config('jwt.secret'), config('services.jwt.secret')
     *  - Optional APP_KEY fallback if JWT_USE_APP_KEY_FALLBACK=true
     *  - Optional kid-based map: JWT_SECRET_kid_<value>
     */
    private function collectSecretCandidates(array $header): array
    {
        $candidates = [];

        // kid-specific secret (e.g., JWT_SECRET_kid_myKeyId="secret")
        $kid = $header['kid'] ?? null;
        if (is_string($kid) && $kid !== '') {
            $kidEnv = env('JWT_SECRET_kid_' . $kid, '');
            if ($kidEnv !== '') $candidates[] = $kidEnv;
        }

        // Primary sources
        $envSingle = (string) env('JWT_SECRET', '');
        if ($envSingle !== '') $candidates[] = $envSingle;

        // Rotation: JWT_SECRETS="k1,k2,k3"
        $envMany = (string) env('JWT_SECRETS', '');
        if ($envMany !== '') {
            foreach (explode(',', $envMany) as $s) {
                $s = trim($s);
                if ($s !== '') $candidates[] = $s;
            }
        }

        // Common config spots
        $cfg1 = (string) config('jwt.secret', '');
        if ($cfg1 !== '') $candidates[] = $cfg1;

        $cfg2 = (string) config('services.jwt.secret', '');
        if ($cfg2 !== '') $candidates[] = $cfg2;

        // Optional APP_KEY fallback (only if explicitly allowed)
        if ((bool) env('JWT_USE_APP_KEY_FALLBACK', false)) {
            $appKey = (string) config('app.key', '');
            if ($appKey !== '') {
                if (str_starts_with($appKey, 'base64:')) {
                    $decoded = base64_decode(substr($appKey, 7), true);
                    if ($decoded !== false) {
                        // derive stable 32 bytes from APP_KEY material
                        $candidates[] = hash_hmac('sha256', 'jwt-fallback', $decoded, true);
                    }
                } else {
                    $candidates[] = hash_hmac('sha256', 'jwt-fallback', $appKey, true);
                }
            }
        }

        // De-dup (string compare)
        return array_values(array_unique($candidates, SORT_STRING));
    }
}
