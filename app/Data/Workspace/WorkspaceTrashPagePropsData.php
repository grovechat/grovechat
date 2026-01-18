<?php

namespace App\Data\Workspace;

use Spatie\LaravelData\Data;

class WorkspaceTrashPagePropsData extends Data
{
    public function __construct(
        /** @var \App\Data\Workspace\WorkspaceTrashListItemData[] */
        public array $workspace_trash_list,
    ) {}
}
