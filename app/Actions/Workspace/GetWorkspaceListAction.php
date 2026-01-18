<?php

namespace App\Actions\Workspace;

use App\Data\Workspace\ListWorkspaceItemData;
use App\Data\Workspace\ShowWorkspaceListPagePropsData;
use App\Models\Workspace;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class GetWorkspaceListAction
{
    use AsAction;

    public function handle(): ShowWorkspaceListPagePropsData
    {
        $workspaces = Workspace::query()
            ->with([
                'owner' => fn ($query) => $query->withTrashed()->select(['id', 'name', 'email']),
            ])
            ->withCount([
                'users' => fn ($query) => $query->withTrashed(),
            ])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Workspace $w) => ListWorkspaceItemData::fromModel($w))
            ->all();

        return new ShowWorkspaceListPagePropsData(
            workspace_list: $workspaces,
        );
    }

    public function asController()
    {
        return Inertia::render('admin/workspace/List', $this->handle()->toArray());
    }
}
