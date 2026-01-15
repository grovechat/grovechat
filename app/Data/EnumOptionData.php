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

    /**
     * @param  array<int, \BackedEnum&\App\Contracts\LabeledEnum>  $cases
     * @return array<int, self>
     */
    public static function fromCases(array $cases): array
    {
        return array_map(
            static fn (BackedEnum&LabeledEnum $enum) => self::fromEnum($enum),
            $cases,
        );
    }
}
