<?php

namespace App\Actions\StorageSetting;

use App\Data\StorageSettingData;
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

        return Inertia::render('systemSettings/StorageSetting', [
            'storageSettings' => $storageSettings,
        ]);
    }
}
