<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->route('tenant_path');
        $tenant = Tenant::where('path', $path)->first();
        if (!$tenant) {
            abort(404, '租户不存在');
        }
        if (!$request->user()->tenants()->where('tenants.id', $tenant->id)->exists()) {
            abort(403, '你不是该租户的成员');
        }

        app()->instance(Tenant::class, $tenant);

        Inertia::share('currentTenant', $tenant);

        return $next($request);
    }
}
