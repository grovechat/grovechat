<?php

namespace App\Actions\StorageSetting;

use App\Data\StorageSetting\ShowGetStorageSettingPagePropsData;
use App\Data\StorageSetting\StorageProfileData;
use App\Data\StorageSetting\StorageProviderConfigData;
use App\Data\StorageSetting\StorageSettingData;
use App\Enums\StorageProvider;
use App\Models\StorageProfile;
use App\Settings\StorageSettings;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class GetStorageSettingAction
{
    use AsAction;

    public function __construct(public StorageSettings $settings) {}

    public function handle(): ShowGetStorageSettingPagePropsData
    {
        $storageSettings = new StorageSettingData(
            enabled: (bool) $this->settings->enabled,
            current_profile_id: $this->settings->current_profile_id,
        );

        $storageProfiles = StorageProfile::query()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (StorageProfile $p) => StorageProfileData::fromModel($p))
            ->all();

        $storageConfig = collect(StorageProvider::cases())
            ->map(fn (StorageProvider $provider) => StorageProviderConfigData::fromProvider($provider))
            ->all();

        return new ShowGetStorageSettingPagePropsData(
            settings: $storageSettings,
            profiles: $storageProfiles,
            providers: $storageConfig,
        );
    }

    public function asController()
    {
        return Inertia::render('admin/storageSetting/Index', $this->handle()->toArray());
    }
}
