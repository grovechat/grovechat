<?php

namespace App\Actions\StorageSetting;

use App\Data\StorageSettingCheckData;
use App\Data\StorageSettingData;
use App\Settings\StorageSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class UpdateStorageSettingAction
{
    use AsAction;

    public function __construct(
        public StorageSettings $settings,
        protected CheckStorageSettingAction $checker,
    ) {}

    public function handle(StorageSettingData $data, bool $secretProvided): void
    {
        // 禁用时只更新 enabled，避免意外覆盖已配置的连接信息
        if (! $data->enabled) {
            $this->settings->enabled = false;
            $this->settings->save();

            return;
        }

        // Secret 允许留空（表示保持原值）；但首次启用且未配置过 secret 时必须提供
        if (! $secretProvided && blank($this->settings->secret)) {
            throw ValidationException::withMessages([
                'secret' => __('storage_settings.secret_required'),
            ]);
        }

        // 保存前先进行连接检测：检测通过才落库
        $secretForCheck = $secretProvided ? $data->secret : $this->settings->secret;
        $checkData = StorageSettingCheckData::from([
            'provider' => $data->provider,
            'key' => $data->key,
            'secret' => $secretForCheck,
            'bucket' => $data->bucket,
            'region' => $data->region,
            'endpoint' => $data->endpoint,
            'url' => $data->url,
        ]);

        try {
            $this->checker->handle($checkData);
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::warning('Storage connection check failed during save', [
                'provider' => $data->provider,
                'region' => $data->region,
                'endpoint' => $data->endpoint,
                'bucket' => $data->bucket,
                'exception' => $e,
            ]);

            $message = __('storage_settings.validation_failed');
            throw ValidationException::withMessages([
                'secret' => $message,
            ]);
        }

        $payload = $data->toArray();

        // Secret 仅在明确提交时覆盖，否则保持原值
        if (! $secretProvided) {
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
