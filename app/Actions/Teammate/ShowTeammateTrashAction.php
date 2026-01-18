<?php

namespace App\Actions\Teammate;

use App\Data\Teammate\UserTrashListItemData;
use App\Data\Teammate\UserTrashPagePropsData;
use App\Models\Workspace;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowTeammateTrashAction
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

        return Inertia::render('teammate/Trash', $props->toArray());
    }
}
