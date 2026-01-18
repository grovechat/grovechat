<?php

namespace App\Data\User;

use App\Data\SimplePaginationData;
use Spatie\LaravelData\Data;

class ShowUserListPagePropsData extends Data
{
    public function __construct(
        /** @var \App\Data\User\ListUserItemData[] */
        public array $user_list,
        public SimplePaginationData $user_list_pagination,
    ) {}
}
