<?php

namespace App\Data\Workspace;

use App\Data\User\UserOptionData;
use Spatie\LaravelData\Data;

class ShowEditWorkspacePagePropsData extends Data
{
    public function __construct(
        public WorkspaceFormData $workspace,
        /** @var UserOptionData[] */
        public array $owner_options,
    ) {}
}
