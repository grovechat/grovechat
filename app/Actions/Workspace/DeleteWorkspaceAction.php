<?php

namespace App\Actions\Workspace;

use Lorisleiva\Actions\Concerns\AsAction;

class DeleteWorkspaceAction
{
    use AsAction;

    public function handle()
    {
        // ...
    }
    
    public function asController()
    {
        return $this->handle();
    }
}
