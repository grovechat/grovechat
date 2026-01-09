<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Features;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        return Inertia::render('Welcome', [
            'canRegister' => Features::enabled(Features::registration()),
        ]);
    }

    public function dashboard()
    {
        /** @var User $user */
        $user = Auth::user();

        $lastWorkspaceSlug = session('last_workspace_slug');
        if ($lastWorkspaceSlug) {
            if ($workspace = $user->workspaces()->where('slug', $lastWorkspaceSlug)->first()) {
                return redirect()->route('workspace.dashboard', ['slug' => $workspace->slug]);
            }
        }

        // 如果没有保存的工作区或无权访问，跳转到第一个工作区
        if ($firstWorkspace = $user->workspaces()->first()) {
            return redirect()->route('workspace.dashboard', ['slug' => $firstWorkspace->slug]);
        }
        return redirect()->route('home');
    }

    public function workspaceHome(Workspace $workspace)
    {
        return redirect()->route('workspace.dashboard', $workspace->slug);
    }

    public function workspaceDashboard()
    {
        return Inertia::render('Dashboard');
    }
}
