<?php

namespace App\Domain\SystemSettings\DTOs;

use Spatie\LaravelData\Data;

class GeneralSettingsData extends Data
{
    public function __construct(
        public string $baseUrl,
        public string $name,
        public ?string $logo = "",
        public ?string $copyright = "",
        public ?string $icpRecord = "",
    )
    {}

    public static function rules(): array
    {
        return [
            'baseUrl' => 'required|string|max:255|url',
            'name' => 'required|string|max:255',
            'logo' => 'nullable|string|max:500|url',
            'copyright' => 'nullable|string|max:255',
            'icpRecord' => 'nullable|string|max:255',
        ];
    }
}
