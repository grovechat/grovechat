<?php

namespace App\Actions\Dashboard;

use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class RedirectLastDashboardAction
{
    use AsAction;

    public function handle()
    {
        // ...
    }

    public function asController(Request $request)
    {
        $lastWorkspaceSlug = session('last_workspace_slug');
        if ($lastWorkspaceSlug) {
            if ($workspace = $request->user()->workspaces()->where('slug', $lastWorkspaceSlug)->first()) {
                return redirect()->route('workspace.dashboard', ['slug' => $workspace->slug]);
            }
        }

        // 如果没有保存的工作区或无权访问，跳转到第一个工作区
        if ($firstWorkspace = $request->user()->workspaces()->first()) {
            return redirect()->route('workspace.dashboard', ['slug' => $firstWorkspace->slug]);
        }

        return redirect()->route('home');
    }
}
