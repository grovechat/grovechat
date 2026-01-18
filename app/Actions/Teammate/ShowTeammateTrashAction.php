<?php

namespace App\Actions\Teammate;

use App\Data\Teammate\ShowTrashTeammatePagePropsData;
use App\Data\Teammate\TrashTeammateData;
use App\Models\Workspace;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowTeammateTrashAction
{
    use AsAction;

    public function handle(Workspace $workspace): ShowTrashTeammatePagePropsData
    {
        $users = $workspace->users()
            ->onlyTrashed()
            ->orderBy('users.deleted_at', 'desc')
            ->get();

        return new ShowTrashTeammatePagePropsData(
            user_list: $users->map(fn ($u) => TrashTeammateData::fromModel($u))->all(),
        );
    }

    public function asController(Workspace $currentWorkspace)
    {
        $props = $this->handle($currentWorkspace);

        return Inertia::render('teammate/Trash', $props->toArray());
    }
}
