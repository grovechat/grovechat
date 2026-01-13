<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class WorkspaceListPagePropsData extends Data
{
    public function __construct(
        /** @var \App\Data\WorkspaceListItemData[] */
        public array $workspace_list,
    ) {}
}

