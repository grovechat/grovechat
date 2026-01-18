<?php

namespace App\Actions\Teammate;

use App\Data\EnumOptionData;
use App\Data\Teammate\UserCreateFormData;
use App\Data\Teammate\UserCreatePagePropsData;
use App\Enums\WorkspaceRole;
use App\Models\Workspace;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowCreateTeammatePageAction
{
    use AsAction;

    public function handle(): UserCreatePagePropsData
    {
        return new UserCreatePagePropsData(
            user_form: new UserCreateFormData,
            role_options: EnumOptionData::fromCases(WorkspaceRole::assignableCases()),
        );
    }

    public function asController(Workspace $currentWorkspace)
    {
        $props = $this->handle();

        return Inertia::render('teammate/Create', $props->toArray());
    }
}
