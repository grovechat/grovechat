<?php

namespace App\Data\GeneralSetting;

use App\Models\Attachment;
use Spatie\LaravelData\Data;

class GeneralSettingsData extends Data
{
    public function __construct(
        public string $baseUrl,
        public string $name,
        public ?string $logoId = null,
        public ?string $copyright = null,
        public ?string $icpRecord = null,
        public ?string $version = null,
        public string $logoUrl = "",
    ) {}

    public static function rules(): array
    {
        return [
            'baseUrl' => 'required|string|max:255|url',
            'name' => 'required|string|max:255',
            'logoId' => 'nullable|string|max:500',
            'copyright' => 'nullable|string|max:255',
            'icpRecord' => 'nullable|string|max:255',
        ];
    }

    public function logoUrl(): string
    {
        return once(fn () => Attachment::find($this->logoId)?->full_url ?? asset('images/logo.png'));
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $data['logo_url'] = $this->logoUrl();

        return $data;
    }
}
