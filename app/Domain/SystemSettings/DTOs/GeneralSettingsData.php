<?php

/**@typescript*/

namespace App\Domain\SystemSettings\DTOs;

use Spatie\LaravelData\Data;

class GeneralSettingsData extends Data
{
    public function __construct(
        public string $baseUrl,
        public string $name,
        public ?string $logo = null,
        public ?string $copyright = null,
        public ?string $icpRecord = null,
        public readonly ?string $version = null,
    )
    {}

    public static function rules(): array
    {
        return [
            'baseUrl' => 'required|string|max:255|url',
            'name' => 'required|string|max:255',
            'logo' => 'nullable|string|max:500',
            'copyright' => 'nullable|string|max:255',
            'icpRecord' => 'nullable|string|max:255',
        ];
    }
}
