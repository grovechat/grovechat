<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class UpdateTenantDTO extends Data
{
    public function __construct(
        public string $name,
        public string $path,
        public ?string $logo = null,
    ) {}

    public function rule()
    {
        return [
            'name' => 'required|string|max:30',
            'path' => 'required|string|max:50',
            'logo' => 'nullable|string',
        ];
    }
}
