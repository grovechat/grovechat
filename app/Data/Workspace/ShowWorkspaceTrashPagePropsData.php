<?php

namespace App\Data\Workspace;

use App\Data\SimplePaginationData;
use Spatie\LaravelData\Data;

class ShowWorkspaceTrashPagePropsData extends Data
{
    public function __construct(
        /** @var \App\Data\Workspace\TrashWorkspaceData[] */
        public array $workspace_trash_list,
        public SimplePaginationData $workspace_trash_list_pagination,
    ) {}
}
