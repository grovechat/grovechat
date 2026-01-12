<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class StorageSettingData extends Data
{
    public function __construct(
        public bool $enabled,
        public ?string $current_profile_id,
    ) {}

    public static function rules(): array
    {
        return [
            'enabled' => 'required|boolean',
            'current_profile_id' => 'nullable|string',
        ];
    }
}
