<?php

namespace App\Actions\Teammate;

use App\Data\EnumOptionData;
use App\Data\Teammate\CreateTeammateFormData;
use App\Data\Teammate\ShowCreateTeammatePagePropsData;
use App\Enums\WorkspaceRole;
use App\Models\Workspace;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowCreateTeammatePageAction
{
    use AsAction;

    public function handle(): ShowCreateTeammatePagePropsData
    {
        return new ShowCreateTeammatePagePropsData(
            user_form: new CreateTeammateFormData,
            role_options: EnumOptionData::fromCases(WorkspaceRole::assignableCases()),
        );
    }

    public function asController(Workspace $currentWorkspace)
    {
        $props = $this->handle();

        return Inertia::render('teammate/Create', $props->toArray());
    }
}
