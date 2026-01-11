<?php

namespace App\Actions\StorageSetting;

use App\Data\StorageSettingData;
use App\Settings\StorageSettings;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateStorageSettingAction
{
    use AsAction;
    
    public function __construct(public StorageSettings $settings)
    {
    }

    public function handle(StorageSettingData $data, bool $secretProvided): void
    {
        // 禁用时只更新 enabled，避免意外覆盖已配置的连接信息
        if (!$data->enabled) {
            $this->settings->enabled = false;
            $this->settings->save();
            return;
        }

        // Secret 允许留空（表示保持原值）；但首次启用且未配置过 secret 时必须提供
        if (!$secretProvided && blank($this->settings->secret)) {
            throw ValidationException::withMessages([
                'secret' => 'Secret Key 不能为空',
            ]);
        }

        $payload = $data->toArray();

        // Secret 仅在明确提交时覆盖，否则保持原值
        if (!$secretProvided) {
            unset($payload['secret']);
        }

        $this->settings->fill($payload)->save();
    }
    
    public function asController(Request $request)
    {
        $data = StorageSettingData::validateAndCreate($request->all());
        $this->handle($data, $request->filled('secret'));

        return back();
    }
}
