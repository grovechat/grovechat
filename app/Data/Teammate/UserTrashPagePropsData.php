<?php

namespace App\Data\Teammate;

use Spatie\LaravelData\Data;

class UserTrashPagePropsData extends Data
{
    public function __construct(
        /** @var \App\Data\UserTrashListItemData[] */
        public array $user_list,
    ) {}
}
