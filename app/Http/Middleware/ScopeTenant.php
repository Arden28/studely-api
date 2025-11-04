<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ScopeTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        // If user has fixed tenant, use it; else allow SuperAdmin to pass X-Tenant-ID
        if ($user && $user->tenant_id) {
            app()->instance('tenant.id', $user->tenant_id);
        } elseif ($tid = $request->header('X-Tenant-ID')) {
            app()->instance('tenant.id', (int)$tid);
        }
        return $next($request);
    }
}
