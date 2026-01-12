<?php

namespace App\Actions\Manage;

use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowCreateWorkspacePageAction
{
    use AsAction;

    public function handle()
    {
        // ...
    }
    
    public function asController()
    {
        return Inertia::render('workspaceSettings/workspace/CreateWorkspace');
    }
}
