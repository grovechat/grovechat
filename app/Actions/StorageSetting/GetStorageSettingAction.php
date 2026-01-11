<?php

namespace App\Actions\StorageSetting;

use App\Data\S3StorageData;
use App\Data\StorageConfigData;
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
        return StorageSettingData::from($this->settings);
    }

    public function asController()
    {
        $storageSettings = $this->handle();
        // 安全起见：不要把已保存的 Secret 回传到前端
        $storageSecretConfigured = filled($this->settings->secret);
        $storageSettings->secret = null;

        $storageConfig = collect(StorageProvider::cases())->map(function ($provider) {
            return StorageConfigData::from([
                'value' => $provider->value,
                'label' => $provider->label(),
                'helpLink' => $provider->getHelpLink(),
                'regions' => $provider->getRegions(),
            ]);
        })->toArray();

        return Inertia::render('systemSettings/StorageSetting', [
            'storageSettings' => $storageSettings,
            'storageConfig' => $storageConfig,
            'storageSecretConfigured' => $storageSecretConfigured,
        ]);
    }
}
