<?php

namespace App\Actions\Workspace;

use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class GetWorkspaceListAction
{
    use AsAction;

    public function handle()
    {
        // ...
    }
    
    public function asController()
    {
        $this->handle();
        
        Inertia::render('workspace/List');
    }
}
