<?php

namespace App\Support;

use App\Contracts\LabeledEnum;
use App\Data\EnumOptionData;
use BackedEnum;

class EnumOptions
{
    /**
     * @param  array<int, \BackedEnum&\App\Contracts\LabeledEnum>  $cases
     * @return array<int, \App\Data\EnumOptionData>
     */
    public static function from(array $cases): array
    {
        return array_map(
            static fn (BackedEnum&LabeledEnum $enum) => EnumOptionData::fromEnum($enum),
            $cases,
        );
    }
}
