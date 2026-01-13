<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class WorkspaceDetailPagePropsData extends Data
{
    public function __construct(
        public WorkspaceDetailData $workspace_detail,
        /** @var \App\Data\WorkspaceMemberData[] */
        public array $workspace_members,
        public SimplePaginationData $workspace_members_pagination,
    ) {}
}

