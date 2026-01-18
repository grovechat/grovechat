<?php

namespace App\Data\Workspace;

use App\Models\Workspace;
use Spatie\LaravelData\Data;

class WorkspaceListItemData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $slug,
        public string $created_at,
        public int $members_count,
        public ?WorkspaceOwnerData $owner,
    ) {}

    public static function fromModel(Workspace $workspace): self
    {
        return new self(
            id: $workspace->id,
            name: $workspace->name,
            slug: $workspace->slug,
            created_at: $workspace->created_at?->toIso8601String() ?? '',
            members_count: (int) ($workspace->users_count ?? 0),
            owner: $workspace->owner ? WorkspaceOwnerData::fromModel($workspace->owner) : null,
        );
    }
}

