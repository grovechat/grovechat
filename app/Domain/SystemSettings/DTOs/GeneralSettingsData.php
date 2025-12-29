<?php

namespace App\Domain\SystemSettings\DTOs;

use Spatie\LaravelData\Data;

class GeneralSettingsData extends Data
{
    public function __construct(
        public string $baseUrl,
        public string $name,
        public ?string $logo = '',
        public ?string $copyright = '',
        public ?string $icpRecord = '',        
    )
    {} 
}