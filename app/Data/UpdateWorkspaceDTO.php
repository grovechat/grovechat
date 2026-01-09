<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class UpdateWorkspaceDTO extends Data
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $logo_id = null,
    ) {}

    public function rule()
    {
        return [
            'name'          => 'required|string|max:30',
            'slug'          => 'required|string|max:50',
            'logo_id'       => 'nullable|string',
        ];
    }
}
