<?php

namespace App\Data\CurrentWorkspace;

use App\Data\SimplePaginationData;
use Spatie\LaravelData\Data;

class WorkspaceMembersData extends Data
{
    public function __construct(
        /** @var \App\Data\CurrentWorkspace\WorkspaceMemberData[] */
        public array $items,
        public SimplePaginationData $pagination,
    ) {}
}
