<?php

namespace App\Data\StorageSetting;

use App\Models\StorageProfile;
use Spatie\LaravelData\Data;

class StorageProfileData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $provider,
        public ?string $bucket,
        public ?string $region,
        public ?string $endpoint,
        public ?string $url,
        public ?string $key_masked,
        public bool $has_secret,
    ) {}

    public static function fromModel(StorageProfile $profile): self
    {
        $key = $profile->key;
        $masked = null;

        if (filled($key)) {
            $key = (string) $key;
            $masked = strlen($key) <= 8
                ? str_repeat('*', max(strlen($key), 4))
                : substr($key, 0, 4).'****'.substr($key, -4);
        }

        return new self(
            id: (string) $profile->id,
            name: (string) $profile->name,
            provider: (string) $profile->provider,
            bucket: $profile->bucket,
            region: $profile->region,
            endpoint: $profile->endpoint,
            url: $profile->url,
            key_masked: $masked,
            has_secret: filled($profile->secret),
        );
    }
}

