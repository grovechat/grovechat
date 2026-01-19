<?php

namespace App\Data\Tag;

use App\Models\Tag;
use Spatie\LaravelData\Data;

class ListTagItemData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $color,
        public ?string $description,
        public string $created_at,
        public string $updated_at,
    ) {}

    public static function fromModel(Tag $tag): self
    {
        return new self(
            id: $tag->id,
            name: $tag->name,
            color: $tag->color,
            description: $tag->description,
            created_at: $tag->created_at?->toIso8601String() ?? '',
            updated_at: $tag->updated_at?->toIso8601String() ?? '',
        );
    }
}
