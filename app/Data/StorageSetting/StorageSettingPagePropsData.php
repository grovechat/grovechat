<?php

namespace App\Data\StorageSetting;

use Spatie\LaravelData\Data;

class StorageSettingPagePropsData extends Data
{
    public function __construct(
        public StorageSettingData $storageSettings,
        /** @var \App\Data\StorageSetting\StorageProfileData[] */
        public array $storageProfiles,
        /** @var \App\Data\StorageSetting\StorageConfigData[] */
        public array $storageConfig,
    ) {}
}
