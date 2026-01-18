<?php

namespace App\Actions\Teammate;

use App\Data\EnumOptionData;
use App\Data\Teammate\ShowCreateTeammatePagePropsData;
use App\Enums\WorkspaceRole;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowCreateTeammatePageAction
{
    use AsAction;

    public function handle(): ShowCreateTeammatePagePropsData
    {
        return new ShowCreateTeammatePagePropsData(
            role_options: EnumOptionData::fromCases(WorkspaceRole::assignableCases()),
        );
    }

    public function asController()
    {
        $props = $this->handle();

        return Inertia::render('teammate/Create', $props->toArray());
    }
}
