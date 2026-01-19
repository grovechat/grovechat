<?php

namespace App\Actions\StorageSetting\StorageProfile;

use App\Actions\StorageSetting\CheckStorageSettingAction;
use App\Data\StorageSetting\FormCheckStorageSettingData;
use App\Data\StorageSetting\FormCreateStorageProfileData;
use App\Models\StorageProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class CreateStorageProfileAction
{
    use AsAction;

    public function __construct(
        protected CheckStorageSettingAction $checker,
    ) {}

    public function handle(FormCreateStorageProfileData $data): StorageProfile
    {
        $checkData = FormCheckStorageSettingData::from([
            'provider' => $data->provider,
            'key' => $data->key,
            'secret' => $data->secret,
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
            throw ValidationException::withMessages([
                'secret' => __('storage_settings.connection_check_failed'),
            ]);
        }

        return StorageProfile::query()->create([
            'name' => $data->name,
            'provider' => $data->provider,
            'region' => $data->region,
            'endpoint' => $data->endpoint,
            'bucket' => $data->bucket,
            'key' => $data->key,
            'secret' => $data->secret,
            'url' => $data->url,
        ]);
    }

    public function asController(Request $request)
    {
        $data = FormCreateStorageProfileData::from($request);
        $this->handle($data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return back();
    }
}
