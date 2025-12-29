<?php

/**@typescript*/

namespace App\Domain\SystemSettings\DTOs;

use Spatie\LaravelData\Data;

class GeneralSettingsData extends Data
{
    public function __construct(
        public string $baseUrl,
        public string $name,
        public string $logo = "",
        public string $copyright = "",
        public string $icpRecord = "",
        public readonly string $version = "",
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
            // version 是只读字段，不需要验证
        ];
    }
}
