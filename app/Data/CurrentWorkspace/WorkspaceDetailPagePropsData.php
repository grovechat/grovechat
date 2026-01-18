<?php

namespace App\Data\CurrentWorkspace;

use App\Data\SimplePaginationData;
use Spatie\LaravelData\Data;

class WorkspaceDetailPagePropsData extends Data
{
    public function __construct(
        public WorkspaceDetailData $workspace_detail,
        /** @var \App\Data\CurrentWorkspace\WorkspaceMemberData[] */
        public array $workspace_members,
        public SimplePaginationData $workspace_members_pagination,
    ) {}
}

