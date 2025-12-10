<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    /**
     * Use as: ->middleware('module:product_module')
     */
    public function handle(Request $request, Closure $next, string $featureKey): Response
    {
        $user = $request->user();
        if($user->role!="admin"){
        if (!$user || !$user->hasModuleFeature($featureKey)) {
            abort(403, "Module disabled: {$featureKey}");
        }
    }
        return $next($request);
    }
}
