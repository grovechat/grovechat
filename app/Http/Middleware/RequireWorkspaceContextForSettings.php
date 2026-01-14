<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireWorkspaceContextForSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        // 超级管理员没有工作区上下文，允许直接访问。
        if ($user->is_super_admin) {
            return $next($request);
        }

        // 普通用户必须携带 from_workspace 才允许访问 /settings/*
        $from = $request->query('from_workspace');
        $from = is_string($from) && $from !== '' ? $from : null;
        if (! $from) {
            return redirect()->route('dashboard');
        }

        if (! $user->workspaces()->where('slug', $from)->exists()) {
            abort(404);
        }

        return $next($request);
    }
}
