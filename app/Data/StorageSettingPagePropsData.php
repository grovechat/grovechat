<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class StorageSettingPagePropsData extends Data
{
    public function __construct(
        public StorageSettingData $storageSettings,
        /** @var \App\Data\StorageConfigData[] */
        public array $storageConfig,
    ) {}
}

