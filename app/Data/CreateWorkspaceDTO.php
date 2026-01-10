<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class CreateWorkspaceDTO extends Data
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $logoId = null,
    ) {}
    
    public function rule()
    {
        return [
            'name'          => 'required|string|max:30',
            'slug'          => 'required|string|max:50',
            'logoId'       => 'nullable|string',
        ];
    }
}
