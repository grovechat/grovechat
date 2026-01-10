<?php

namespace App\Actions\Workspace;

use App\Models\User;
use App\Models\Workspace;
use Lorisleiva\Actions\Concerns\AsAction;

class GetDefaultWorkspaceAction
{
    use AsAction;

    public function handle(User $user)
    {
        return Workspace::query()->where('owner_id', $user->id)->firstOrFail(); 
    }
}
