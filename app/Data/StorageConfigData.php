<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class StorageConfigData extends Data
{
    public function __construct(
        public string $value,
        public string $label,
        public string $helpLink,
        /** @var \App\Data\StorageRegionData[] */
        public array $regions,
    ) {}
}
