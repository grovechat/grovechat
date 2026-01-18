<?php

namespace App\Actions\Workspace;

use App\Data\User\UserOptionData;
use App\Data\Workspace\ShowEditWorkspacePagePropsData;
use App\Data\Workspace\WorkspaceFormData;
use App\Models\User;
use App\Models\Workspace;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowEditWorkspacePageAction
{
    use AsAction;

    public function handle(string $id): ShowEditWorkspacePagePropsData
    {
        $workspace = Workspace::query()->findOrFail($id);

        $owners = User::query()
            ->where('is_super_admin', false)
            ->orderBy('id')
            ->get(['id', 'name', 'email'])
            ->map(fn (User $u) => UserOptionData::fromModel($u))
            ->all();

        return new ShowEditWorkspacePagePropsData(
            workspace: WorkspaceFormData::fromModel($workspace),
            owner_options: $owners,
        );
    }

    public function asController(string $id)
    {
        return Inertia::render('admin/workspace/Edit', $this->handle($id)->toArray());
    }
}
