<?php

namespace App\Data\Workspace;

use Spatie\LaravelData\Data;

class ShowWorkspaceTrashPagePropsData extends Data
{
    public function __construct(
        /** @var \App\Data\Workspace\TrashWorkspaceItemData[] */
        public array $workspace_trash_list,
    ) {}
}
