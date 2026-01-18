<?php

namespace App\Data\Teammate;

use Spatie\LaravelData\Data;

class ShowCreateTeammatePagePropsData extends Data
{
    public function __construct(
        /** @var \App\Data\EnumOptionData[] */
        public array $role_options,
    ) {}
}
