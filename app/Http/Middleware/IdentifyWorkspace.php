<?php

namespace App\Http\Middleware;

use App\Data\WorkspaceUserContextData;
use App\Models\User;
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
        $slug = $request->route('slug');
        $workspace = Workspace::where('slug', $slug)->first();
        if (! $workspace) {
            abort(404, '工作区不存在');
        }
        if (! $request->user()->workspaces()->where('workspaces.id', $workspace->id)->exists()) {
            abort(403, '你不是该工作区的成员');
        }

        app()->instance(Workspace::class, $workspace);

        Inertia::share('currentWorkspace', $workspace);

        /** @var User $user */
        $user = $request->user();
        app()->instance(WorkspaceUserContextData::class, WorkspaceUserContextData::fromModels($workspace, $user));

        if ($request->is('w/*/manage*')) {
            Gate::authorize('workspace.canAccessManageCenter', [$workspace]);
        }

        return $next($request);
    }
}
