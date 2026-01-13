<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class UserListPagePropsData extends Data
{
    public function __construct(
        /** @var \App\Data\UserListItemData[] */
        public array $user_list,
    ) {}
}
