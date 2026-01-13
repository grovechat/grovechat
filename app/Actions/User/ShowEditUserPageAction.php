<?php

namespace App\Actions\User;

use App\Data\UserEditFormData;
use App\Data\UserEditPagePropsData;
use App\Models\Workspace;
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

        return new UserEditPagePropsData(
            user_form: UserEditFormData::fromModel($user),
        );
    }

    public function asController(Workspace $currentWorkspace, string $slug, string $id)
    {
        $props = $this->handle($currentWorkspace, $id);

        return Inertia::render('user/Edit', $props->toArray());
    }
}
