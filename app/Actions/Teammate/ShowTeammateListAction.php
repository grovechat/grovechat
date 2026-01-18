<?php

namespace App\Actions\Teammate;

use App\Data\EnumOptionData;
use App\Data\Teammate\ShowListTeammatePagePropsData;
use App\Data\WorkspaceUserContextData;
use App\Enums\UserOnlineStatus;
use App\Models\Workspace;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowTeammateListAction
{
    use AsAction;

    public function handle(Workspace $workspace): ShowListTeammatePagePropsData
    {
        $users = $workspace->users()
            ->orderBy('users.id', 'asc')
            ->get();

        $userList = $users->map(fn ($u) => WorkspaceUserContextData::fromModels($workspace, $u)
            ->withShowDeleteButton(Gate::allows('workspace-users.deleteUser', [$workspace, $u]))
        )->all();

        return new ShowListTeammatePagePropsData(
            user_list: $userList,
            online_status_options: EnumOptionData::fromCases(UserOnlineStatus::cases()),
            can_restore_user: Gate::allows('workspace-users.restoreUser', [$workspace]),
        );
    }

    public function asController(Workspace $currentWorkspace)
    {
        $props = $this->handle($currentWorkspace);

        return Inertia::render('teammate/List', $props->toArray());
    }
}
