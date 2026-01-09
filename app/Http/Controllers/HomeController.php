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

        $lastWorkspacePath = session('last_workspace_path');
        if ($lastWorkspacePath) {
            $workspace = $user->workspaces()->where('path', $lastWorkspacePath)->first();
            if ($workspace) {
                return redirect()->route('workspace.dashboard', ['workspace_path' => $workspace->path]);
            }
        }

        // 如果没有保存的工作区或无权访问，跳转到第一个工作区
        $firstWorkspace = $user->workspaces()->first();
        if ($firstWorkspace) {
            return redirect()->route('workspace.dashboard', ['workspace_path' => $firstWorkspace->path]);
        }
        return redirect()->route('home');
    }

    public function workspaceHome(Workspace $workspace)
    {
        return redirect()->route('workspace.dashboard', $workspace->path);
    }

    public function workspaceDashboard()
    {
        return Inertia::render('Dashboard');
    }
}
