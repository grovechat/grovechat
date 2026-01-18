<?php

namespace App\Data\CurrentWorkspace;

use Spatie\LaravelData\Data;

class ShowWorkspaceDetailPagePropsData extends Data
{
    public function __construct(
        public WorkspaceDetailData $workspace,
        public WorkspaceMembersData $members,
    ) {}
}
