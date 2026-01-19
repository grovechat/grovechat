<?php

namespace App\Data\StorageSetting;

use App\Data\EnumOptionData;
use App\Enums\StorageProvider;
use Spatie\LaravelData\Data;

class StorageProviderConfigData extends Data
{
    public function __construct(
        public EnumOptionData $provider,
        public string $helpLink,
        /** @var \App\Data\StorageSetting\StorageRegionData[] */
        public array $regions,
    ) {}

    public static function fromProvider(StorageProvider $provider): self
    {
        $regions = array_map(
            static fn (array $region) => StorageRegionData::from($region),
            $provider->getRegions(),
        );

        return new self(
            provider: EnumOptionData::fromEnum($provider),
            helpLink: $provider->getHelpLink(),
            regions: $regions,
        );
    }
}
