<?php

namespace App\Data\Teammate;

use App\Data\User\UserOptionData;
use Spatie\LaravelData\Data;

class ShowCreateTeammatePagePropsData extends Data
{
    public function __construct(
        /** @var \App\Data\EnumOptionData[] */
        public array $role_options,

        /** @var array<int, UserOptionData> */
        public array $available_users,
    ) {}
}
