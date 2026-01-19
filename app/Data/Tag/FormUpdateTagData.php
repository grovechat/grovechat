<?php

namespace App\Data\Tag;

use Spatie\LaravelData\Data;

class FormUpdateTagData extends Data
{
    public function __construct(
        public string $name,
        public ?string $color = null,
        public ?string $description = null,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }
}
