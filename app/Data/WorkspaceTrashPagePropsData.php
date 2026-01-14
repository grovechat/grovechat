<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class WorkspaceTrashPagePropsData extends Data
{
    public function __construct(
        /** @var \App\Data\WorkspaceTrashListItemData[] */
        public array $workspace_trash_list,
    ) {}
}
