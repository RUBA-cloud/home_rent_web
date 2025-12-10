<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    /**
     * Use as: ->middleware('perm:product,can_edit')
     * $moduleName must match permissions.module_name (e.g., 'product', 'order', 'category', ...)
     * $ability one of: can_add | can_edit | can_delete | can_view_history
     */
    public function handle(Request $request, Closure $next, string $moduleName = null, string $ability = null): Response
    {
        // Extra safety if parameters weren't passed correctly.
        if ($moduleName === null || $ability === null) {
            // Try to recover from older Laravel calling style (very rare)
            $args = array_slice(func_get_args(), 2);
            $moduleName = $moduleName ?? ($args[0] ?? null);
            $ability    = $ability    ?? ($args[1] ?? null);
        }

        if (!$moduleName || !$ability) {
            abort(500, 'EnsurePermission middleware misconfigured: missing moduleName or ability.');
        }

        $user = $request->user();
        if (!$user || !$user->hasPermission($moduleName, $ability)) {

            abort(403, "Permission denied: {$moduleName} / {$ability}");
        }

        return $next($request);
    }
}
