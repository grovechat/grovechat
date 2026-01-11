<?php

namespace App\Actions\StorageSetting;

use App\Data\StorageConfigData;
use App\Data\StorageSettingPagePropsData;
use App\Data\StorageSettingData;
use App\Enums\StorageProvider;
use App\Settings\StorageSettings;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class GetStorageSettingAction
{
    use AsAction;

    public function __construct(public StorageSettings $settings)
    {
    }

    public function handle()
    {
        $storageSettings = StorageSettingData::from($this->settings);

        // 安全起见：不要把已保存的 Secret 回传到前端
        $storageSettings->secret = null;

        $storageConfig = collect(StorageProvider::cases())->map(function ($provider) {
            return StorageConfigData::from([
                'value' => $provider->value,
                'label' => $provider->label(),
                'helpLink' => $provider->getHelpLink(),
                'regions' => $provider->getRegions(),
            ]);
        })->all();

        return new StorageSettingPagePropsData(
            storageSettings: $storageSettings,
            storageConfig: $storageConfig,
        );
    }

    public function asController()
    {
        return Inertia::render('systemSettings/StorageSetting', $this->handle()->toArray());
    }
}
