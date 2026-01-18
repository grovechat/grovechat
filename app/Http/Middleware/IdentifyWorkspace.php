<?php

namespace App\Http\Middleware;

use App\Data\WorkspaceUserContextData;
use App\Models\Workspace;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class IdentifyWorkspace
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->is_super_admin) {
            return $next($request);
        }

        // 获取当前用户所有工作区
        $workspaces = collect();
        $workspaces = $request->user()->workspaces()->get();
        Inertia::share('workspaces', $workspaces);
        
        // 根据slug或者from_workspace设置当前用户所在工作区
        $slug = $request->route('slug');
        $path = '/'.ltrim($request->path(), '/');
        $isSettingsPath = str_starts_with($path, '/settings');   
        if ($isSettingsPath) {
            $from = $request->query('from_workspace');
            $hasFromWorkspace = is_string($from) && $from !== '';
            if ($hasFromWorkspace) {
                $workspace = $workspaces->firstWhere('slug', $from);
            }
        } else {
            $workspace = $workspaces->firstWhere('slug', $slug);
        }
        if (empty($workspace)) {
            abort(404, '工作区不存在');
        }
        if (! $request->user()->workspaces()->where('workspaces.id', $workspace->id)->exists()) {
            abort(403, '你不是该工作区的成员');
        }
        app()->instance(Workspace::class, $workspace);
        Inertia::share('currentWorkspace', $workspace);
        
        // 设置工作区用户上下文
        app()->instance(WorkspaceUserContextData::class, WorkspaceUserContextData::fromModels($workspace, $request->user()));
        
        // 授权
        Inertia::share('canAccessManageCenter', Gate::allows('workspace.canAccessManageCenter', [$workspace]));
        if ($request->is('w/*/manage*')) {
            Gate::authorize('workspace.canAccessManageCenter', [$workspace]);
        }
        
        // 返回
        return $next($request);
    }
}
