<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class SystemUserListPagePropsData extends Data
{
    public function __construct(
        /** @var \App\Data\SystemUserListItemData[] */
        public array $user_list,
        public SimplePaginationData $user_list_pagination,
    ) {}
}
