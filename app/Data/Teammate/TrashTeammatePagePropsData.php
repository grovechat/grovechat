<?php

namespace App\Data\Teammate;

use Spatie\LaravelData\Data;

class TrashTeammatePagePropsData extends Data
{
    public function __construct(
        /** @var \App\Data\Teammate\TrashTeammateItemData[] */
        public array $user_list,
    ) {}
}
