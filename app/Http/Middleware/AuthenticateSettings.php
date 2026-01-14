<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateSettings
{
    public function handle(Request $request, Closure $next): Response
    {
        $from = $request->query('from_workspace');
        $hasFromWorkspace = is_string($from) && $from !== '';

        // 带 from_workspace：强制使用 web（工作区用户）
        if ($hasFromWorkspace) {
            if (! Auth::guard('web')->check()) {
                return redirect()->route('login', absolute: false);
            }

            Auth::shouldUse('web');

            return $next($request);
        }

        // 不带 from_workspace：优先使用 admin（系统管理员），否则回退到 web（普通用户访问但缺上下文，交给 EnsureSettingsWorkspace 处理）
        if (Auth::guard('admin')->check()) {
            Auth::shouldUse('admin');

            return $next($request);
        }

        if (Auth::guard('web')->check()) {
            Auth::shouldUse('web');

            return $next($request);
        }

        return redirect()->route('login', absolute: false);
    }
}
