<?php

namespace App\Data\Workspace;

use Spatie\LaravelData\Data;

class ShowWorkspaceListPagePropsData extends Data
{
    public function __construct(
        /** @var \App\Data\Workspace\WorkspaceData[] */
        public array $workspace_list,
    ) {}
}
