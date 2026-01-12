<?php

namespace App\Actions\Dashboard;

use App\Models\Workspace;
use Lorisleiva\Actions\Concerns\AsAction;

class RedirectCurrentWorkspaceDashboard
{
    use AsAction;

    public function handle()
    {
        // ...
    }

    public function asController(Workspace $currentWorkspace)
    {
        return redirect()->route('workspace.dashboard', $currentWorkspace->slug);
    }
}
