<?php

namespace App\Actions\User;

use App\Data\UserTrashListItemData;
use App\Data\UserTrashPagePropsData;
use App\Models\Workspace;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowUserTrashAction
{
    use AsAction;

    public function handle(Workspace $workspace): UserTrashPagePropsData
    {
        $users = $workspace->users()
            ->onlyTrashed()
            ->orderBy('users.deleted_at', 'desc')
            ->get();

        return new UserTrashPagePropsData(
            user_list: $users->map(fn ($u) => UserTrashListItemData::fromModel($u))->all(),
        );
    }

    public function asController(Workspace $currentWorkspace)
    {
        $props = $this->handle($currentWorkspace);

        return Inertia::render('user/Trash', $props->toArray());
    }
}
