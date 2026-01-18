<?php

namespace App\Actions\Teammate;

use App\Data\Teammate\TrashTeammateItemData;
use App\Data\Teammate\TrashTeammatePagePropsData;
use App\Models\Workspace;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowTeammateTrashAction
{
    use AsAction;

    public function handle(Workspace $workspace): TrashTeammatePagePropsData
    {
        $users = $workspace->users()
            ->onlyTrashed()
            ->orderBy('users.deleted_at', 'desc')
            ->get();

        return new TrashTeammatePagePropsData(
            user_list: $users->map(fn ($u) => TrashTeammateItemData::fromModel($u))->all(),
        );
    }

    public function asController(Workspace $currentWorkspace)
    {
        $props = $this->handle($currentWorkspace);

        return Inertia::render('teammate/Trash', $props->toArray());
    }
}
