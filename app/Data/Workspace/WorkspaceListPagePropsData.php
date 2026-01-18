<?php

namespace App\Data\Workspace;

use Spatie\LaravelData\Data;

class WorkspaceListPagePropsData extends Data
{
    public function __construct(
        /** @var \App\Data\Workspace\WorkspaceListItemData[] */
        public array $workspace_list,
    ) {}
}

