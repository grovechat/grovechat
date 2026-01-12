<?php

namespace App\Actions\StorageSetting;

use App\Data\StorageSettingCheckData;
use App\Enums\StorageProvider;
use App\Settings\StorageSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class CheckStorageSettingAction
{
    use AsAction;

    public function __construct(
        public StorageSettings $settings,
    ) {}

    public function handle(StorageSettingCheckData $data)
    {
        $payload = $data->toArray();

        if (! filled($payload['secret'] ?? null)) {
            throw ValidationException::withMessages([
                'secret' => __('storage_settings.secret_required'),
            ]);
        }

        $config = [
            'driver' => 's3',
            'key' => $payload['key'],
            'secret' => $payload['secret'],
            'region' => $payload['region'] ?? 'us-east-1',
            'bucket' => $payload['bucket'],
            'url' => $payload['url'] ?? null,
            'endpoint' => $payload['endpoint'] ?? null,
            'use_path_style_endpoint' => ($payload['provider'] ?? null) === StorageProvider::MINIO->value,
            'throw' => true,
        ];

        $diskName = 'storage_check_'.md5(uniqid('', true));
        config(["filesystems.disks.{$diskName}" => $config]);

        Storage::disk($diskName)->files('/', false);
    }

    public function asController(Request $request)
    {
        $data = StorageSettingCheckData::from($request);

        // 如果用户未填写 secret，则尝试使用系统已保存的 secret
        if (! filled($data->secret)) {
            $data->secret = $this->settings->secret;
        }

        try {
            $this->handle($data);

            Inertia::flash('toast', [
                'type' => 'success',
                'message' => __('storage_settings.check_success'),
            ]);
        } catch (ValidationException $e) {
            // 让 Inertia 正常回传字段错误
            throw $e;
        } catch (Throwable $e) {
            Log::warning('Storage connection check failed', [
                'provider' => $data->provider ?? null,
                'region' => $data->region ?? null,
                'endpoint' => $data->endpoint ?? null,
                'bucket' => $data->bucket ?? null,
                'exception' => $e,
            ]);
            $message = __('storage_settings.validation_failed');

            Inertia::flash('toast', [
                'type' => 'warning',
                'message' => $message,
            ]);

            return back()->withErrors([
                'secret' => $message,
            ]);
        }

        return back();
    }
}
