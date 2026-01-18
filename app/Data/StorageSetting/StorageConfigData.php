<?php

namespace App\Data\StorageSetting;

use Spatie\LaravelData\Data;

class StorageConfigData extends Data
{
    public function __construct(
        public string $value,
        public string $label,
        public string $helpLink,
        /** @var \App\Data\StorageSetting\StorageRegionData[] */
        public array $regions,
    ) {}
}
