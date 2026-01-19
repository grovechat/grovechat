<?php

namespace App\Actions\Dashboard;

use App\Data\WorkspaceUserContextData;
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
        $ctx = WorkspaceUserContextData::fromRequest($request);

        return redirect()->route('workspace.dashboard', $ctx->workspaceSlug());
    }
}
