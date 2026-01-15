<?php

namespace App\Actions\User;

use App\Data\EnumOptionData;
use App\Data\UserEditFormData;
use App\Data\UserEditPagePropsData;
use App\Enums\WorkspaceRole;
use App\Models\Workspace;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowEditUserPageAction
{
    use AsAction;

    public function handle(Workspace $workspace, string $id): UserEditPagePropsData
    {
        $user = $workspace->users()
            ->whereKey($id)
            ->firstOrFail();

        $canUpdateRole = Gate::allows('workspace-users.canUpdateRole', [$workspace, $user]);

        return new UserEditPagePropsData(
            user_form: UserEditFormData::fromModel($user),
            role_options: EnumOptionData::fromCases(WorkspaceRole::assignableCases()),
            can_update_role: $canUpdateRole,
            can_update_password: Gate::allows('workspace-users.updatePassword', [$workspace]),
        );
    }

    public function asController(Workspace $currentWorkspace, string $slug, string $id)
    {
        $props = $this->handle($currentWorkspace, $id);

        return Inertia::render('user/Edit', $props->toArray());
    }
}
