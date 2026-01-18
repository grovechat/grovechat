<?php

namespace App\Actions\StorageSetting\StorageProfile;

use App\Actions\StorageSetting\CheckStorageSettingAction;
use App\Data\StorageSetting\StorageProfileCreateData;
use App\Data\StorageSetting\StorageSettingCheckData;
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

    public function handle(StorageProfileCreateData $data): StorageProfile
    {
        $checkData = StorageSettingCheckData::from([
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
        $data = StorageProfileCreateData::validateAndCreate($request->all());
        $this->handle($data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('storage_settings.profile_created'),
        ]);

        return back();
    }
}
