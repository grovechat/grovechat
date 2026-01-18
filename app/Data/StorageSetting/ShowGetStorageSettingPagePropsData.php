<?php

namespace App\Data\StorageSetting;

use Spatie\LaravelData\Data;

class ShowGetStorageSettingPagePropsData extends Data
{
    public function __construct(
        public StorageSettingData $settings,

        /** @var \App\Data\StorageSetting\StorageProfileData[] */
        public array $profiles,

        /** @var \App\Data\StorageSetting\StorageProviderConfigData[] */
        public array $providers,
    ) {}
}
