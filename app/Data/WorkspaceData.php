<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class WorkspaceData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public string $logoUrl,
        public ?string $ownerId = null,
        public ?string $logoId = null,
    ) {}
}
