<?php

namespace App\Actions\Workspace;

use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class GetWorkspaceGeneralSettingAction
{
    use AsAction;

    public function asController()
    {
        return Inertia::render('workspaceSettings/workspace/General');
    }
}
