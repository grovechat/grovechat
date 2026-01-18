<?php

namespace App\Actions\Workspace;

use App\Data\User\UserOptionData;
use App\Data\Workspace\ShowCreateWorkspacePagePropsData;
use App\Models\User;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowCreateWorkspacePageAction
{
    use AsAction;

    public function handle(): ShowCreateWorkspacePagePropsData
    {
        $owners = User::query()
            ->where('is_super_admin', false)
            ->orderBy('id')
            ->get(['id', 'name', 'email'])
            ->map(fn (User $u) => UserOptionData::fromModel($u))
            ->all();

        return new ShowCreateWorkspacePagePropsData(
            owner_options: $owners,
        );
    }

    public function asController()
    {
        return Inertia::render('admin/workspace/Create', $this->handle()->toArray());
    }
}
