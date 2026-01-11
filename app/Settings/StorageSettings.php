<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class StorageSettings extends Settings
{
    public bool $enabled = false;

    public ?string $provider;

    public ?string $key;

    public ?string $secret;

    public ?string $bucket;

    public ?string $region;

    public ?string $endpoint;

    public ?string $url; // CDN 地址或自定义域名 (如 https://cdn.example.com)

    public static function group(): string
    {
        return 'storage';
    }
}
