<?php

namespace App\Actions\StorageSetting;

use App\Data\StorageSettingData;
use App\Settings\StorageSettings;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateStorageSettingAction
{
    use AsAction;
    
    public function __construct(public StorageSettings $settings)
    {
    }

    public function handle(StorageSettingData $data)
    {
        $this->settings->fill($data->toArray());
        $this->settings->save();
    }
    
    public function asController(Request $request)
    {
        $data = StorageSettingData::from($request->all());
        $this->handle($data); 
        return back();
    }
}
