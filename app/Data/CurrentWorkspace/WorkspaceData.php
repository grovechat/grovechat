<?php

namespace App\Data\CurrentWorkspace;

use App\Enums\WorkspaceRole;
use App\Models\Workspace;
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
        public ?WorkspaceRole $role = null,
    ) {}

    public static function fromModel(Workspace $workspace): self
    {
        $role = WorkspaceRole::tryFrom((string) ($workspace->pivot?->role ?? '')) ?? null;

        return new self(
            id: (string) $workspace->id,
            name: $workspace->name,
            slug: $workspace->slug,
            logoUrl: (string) $workspace->logo_url,
            ownerId: filled($workspace->owner_id) ? (string) $workspace->owner_id : null,
            logoId: filled($workspace->logo_id) ? (string) $workspace->logo_id : null,
            role: $role,
        );
    }
}
