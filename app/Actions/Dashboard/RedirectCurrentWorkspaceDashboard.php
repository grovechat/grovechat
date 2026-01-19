<?php

namespace App\Actions\Dashboard;

use App\Http\RequestContexts\WorkspaceRequestContext;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class RedirectCurrentWorkspaceDashboard
{
    use AsAction;

    public function handle()
    {
        // ...
    }

    public function asController(Request $request)
    {
        $currentWorkspace = WorkspaceRequestContext::fromRequest($request)->workspace;

        return redirect()->route('workspace.dashboard', $currentWorkspace->slug);
    }
}
