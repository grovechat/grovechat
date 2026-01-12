<?php

namespace App\Actions\StorageSetting;

use App\Data\StorageSettingCheckData;
use App\Enums\StorageProvider;
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
        $config = collect($config)->reject(fn ($v) => $v === null)->all();

        Storage::build($config)->files('/', false);
    }

    public function asController(Request $request)
    {
        $data = StorageSettingCheckData::validateAndCreate($request->all());

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
