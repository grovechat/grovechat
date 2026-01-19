<?php

namespace App\Actions\StorageSetting\StorageProfile;

use App\Actions\StorageSetting\CheckStorageSettingAction;
use App\Data\StorageSetting\FormCheckStorageSettingData;
use App\Data\StorageSetting\FormUpdateStorageProfileData;
use App\Models\StorageProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class UpdateStorageProfileAction
{
    use AsAction;

    public function __construct(
        protected CheckStorageSettingAction $checker,
    ) {}

    public function handle(StorageProfile $profile, FormUpdateStorageProfileData $data): void
    {
        $update = [
            'name' => $data->name,
            'url' => $data->url,
        ];

        // 只有同时提交 key+secret 才更新凭证（避免只改一半）
        $key = filled($data->key) ? $data->key : null;
        $secret = filled($data->secret) ? $data->secret : null;
        $keyProvided = filled($key);
        $secretProvided = filled($secret);
        if ($keyProvided || $secretProvided) {
            if (! $keyProvided || ! $secretProvided) {
                throw ValidationException::withMessages([
                    'secret' => __('storage_settings.profile_credentials_pair_required'),
                ]);
            }

            $checkData = FormCheckStorageSettingData::from([
                'provider' => $profile->provider,
                'key' => $key,
                'secret' => $secret,
                'bucket' => $profile->bucket,
                'region' => $profile->region,
                'endpoint' => $profile->endpoint,
                'url' => $data->url ?? $profile->url,
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

            $update['key'] = $key;
            $update['secret'] = $secret;
        }

        $profile->update([
            ...$update,
        ]);
    }

    public function asController(Request $request, StorageProfile $profile)
    {
        $data = FormUpdateStorageProfileData::validateAndCreate($request->all());
        $this->handle($profile, $data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return back();
    }
}
