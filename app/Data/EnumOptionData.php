<?php

namespace App\Data;

use App\Contracts\LabeledEnum;
use BackedEnum;
use Spatie\LaravelData\Data;

class EnumOptionData extends Data
{
    public function __construct(
        public string|int $value,
        public string $label,
    ) {}

    public static function fromEnum(BackedEnum&LabeledEnum $enum): self
    {
        return new self(
            value: $enum->value,
            label: $enum->label(),
        );
    }
}
