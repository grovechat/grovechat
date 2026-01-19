<?php

namespace App\Data\CurrentWorkspace;

use App\Data\EnumOptionData;
use App\Data\User\UserOptionData;
use Spatie\LaravelData\Data;

class ShowWorkspaceDetailPagePropsData extends Data
{
    public function __construct(
        public WorkspaceDetailData $workspace,
        public WorkspaceMembersData $members,
        /** @var EnumOptionData[] */
        public array $role_options = [],
        /** @var UserOptionData[] */
        public array $available_users = [],
    ) {}
}
