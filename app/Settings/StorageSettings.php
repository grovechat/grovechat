<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class StorageSettings extends Settings
{
    public bool $enabled = false;

    public ?string $current_profile_id;

    public static function group(): string
    {
        return 'storage';
    }
}
