<?php

namespace App\Http\Middleware;

use App\Models\Workspace;
use Closure;
use Illuminate\Http\Request;
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
        if (!$workspace) {
            abort(404, '工作区不存在');
        }
        if (!$request->user()->workspaces()->where('workspaces.id', $workspace->id)->exists()) {
            abort(403, '你不是该工作区的成员');
        }

        app()->instance(Workspace::class, $workspace);

        Inertia::share('currentWorkspace', $workspace);

        return $next($request);
    }
}
