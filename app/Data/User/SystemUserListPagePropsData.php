<?php

namespace App\Data\User;

use Spatie\LaravelData\Data;

class SystemUserListPagePropsData extends Data
{
    public function __construct(
        /** @var \App\Data\User\SystemUserListItemData[] */
        public array $user_list,
        public SimplePaginationData $user_list_pagination,
    ) {}
}
