<?php

namespace App\Actions\User;

use App\Data\UserListItemData;
use App\Data\UserListPagePropsData;
use App\Models\Workspace;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowUserListAction
{
    use AsAction;

    public function handle(Workspace $workspace): UserListPagePropsData
    {
        $users = $workspace->users()
            ->orderBy('users.id', 'asc')
            ->get();

        return new UserListPagePropsData(
            user_list: $users->map(fn ($u) => UserListItemData::fromModel($u))->all(),
        );
    }

    public function asController(Workspace $currentWorkspace)
    {
        $props = $this->handle($currentWorkspace);

        return Inertia::render('user/List', $props->toArray());
    }
}
