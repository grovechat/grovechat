<?php

namespace App\Actions\Workspace;

use App\Data\Workspace\ShowWorkspaceTrashPagePropsData;
use App\Data\Workspace\TrashWorkspaceItemData;
use App\Models\Workspace;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class GetWorkspaceTrashListAction
{
    use AsAction;

    public function handle(): ShowWorkspaceTrashPagePropsData
    {
        $workspaces = Workspace::onlyTrashed()
            ->with([
                'owner' => fn ($query) => $query->withTrashed()->select(['id', 'name', 'email']),
            ])
            ->withCount([
                'users' => fn ($query) => $query->withTrashed(),
            ])
            ->orderByDesc('deleted_at')
            ->get()
            ->map(fn (Workspace $w) => TrashWorkspaceItemData::fromModel($w))
            ->all();

        return new ShowWorkspaceTrashPagePropsData(
            workspace_trash_list: $workspaces,
        );
    }

    public function asController()
    {
        return Inertia::render('admin/workspace/Trash', $this->handle()->toArray());
    }
}
