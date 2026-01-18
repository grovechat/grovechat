<?php

namespace App\Actions\Teammate;

use App\Data\EnumOptionData;
use App\Data\Teammate\ShowEditTeammatePagePropsData;
use App\Data\Teammate\TeammateData;
use App\Enums\WorkspaceRole;
use App\Models\Workspace;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowEditTeammatePageAction
{
    use AsAction;

    public function handle(Workspace $workspace, string $id): ShowEditTeammatePagePropsData
    {
        $user = $workspace->users()
            ->whereKey($id)
            ->firstOrFail();

        return new ShowEditTeammatePagePropsData(
            user_form: TeammateData::fromModel($user),
            role_options: EnumOptionData::fromCases(WorkspaceRole::assignableCases()),
            can_update_profile: Gate::allows('workspace-users.updateProfile', [$workspace, $user]),
            can_update_email: Gate::allows('workspace-users.updateEmail', [$workspace, $user]),
            can_update_password: Gate::allows('workspace-users.updatePassword', [$workspace, $user]),
            can_update_role: Gate::allows('workspace-users.canUpdateRole', [$workspace, $user]),
        );
    }

    public function asController(Workspace $currentWorkspace, string $slug, string $id)
    {
        $props = $this->handle($currentWorkspace, $id);

        return Inertia::render('teammate/Edit', $props->toArray());
    }
}
