<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackLastTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 如果用户已登录且当前路由包含 tenant_path，保存到 session
        if ($request->user() && $request->route('tenant_path')) {
            $tenantPath = $request->route('tenant_path');
            $request->session()->put('last_tenant_path', $tenantPath);
        }

        return $next($request);
    }
}
