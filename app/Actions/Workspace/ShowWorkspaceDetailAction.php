<?php

namespace App\Actions\Workspace;

use Lorisleiva\Actions\Concerns\AsAction;
use Inertia\Inertia;

class ShowWorkspaceDetailAction
{
    use AsAction;

    public function handle()
    {
        // ...
    }
    
    public function asController()
    {
        $this->handle();
        
        Inertia::render('workspace/Show');
    }
}
