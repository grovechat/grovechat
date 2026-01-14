<?php

namespace App\Actions\Workspace;

use App\Data\WorkspaceListItemData;
use App\Data\WorkspaceListPagePropsData;
use App\Models\Workspace;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class GetWorkspaceListAction
{
    use AsAction;

    public function handle()
    {
        $workspaces = Workspace::query()
            ->with(['owner:id,name,email'])
            ->withCount('users')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Workspace $w) => WorkspaceListItemData::fromModel($w))
            ->all();

        return new WorkspaceListPagePropsData(
            workspace_list: $workspaces,
        );
    }

    public function asController()
    {
        return Inertia::render('admin/workspace/List', $this->handle()->toArray());
    }
}
