<?php

namespace App\Actions\StorageSetting;

use App\Data\StorageSettingData;
use App\Models\StorageProfile;
use App\Services\Storage\StorageProfileDisk;
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
    ) {}

    public function handle(StorageSettingData $data): void
    {
        if (! $data->enabled) {
            $this->settings->enabled = false;
            $this->settings->save();

            return;
        }

        if (! filled($data->current_profile_id)) {
            throw ValidationException::withMessages([
                'current_profile_id' => '启用对象存储必须选择一个存储配置',
            ]);
        }

        $profile = StorageProfile::query()->find($data->current_profile_id);
        if (! $profile) {
            throw ValidationException::withMessages([
                'current_profile_id' => '所选存储配置不存在',
            ]);
        }

        if (! filled($profile->key) || ! filled($profile->secret)) {
            throw ValidationException::withMessages([
                'current_profile_id' => '所选存储配置缺少 Key/Secret，请先更新凭证',
            ]);
        }

        try {
            StorageProfileDisk::build($profile)->files('/', false);
        } catch (Throwable $e) {
            Log::warning('Storage profile connection check failed during save', [
                'storage_profile_id' => $profile->id,
                'exception' => $e,
            ]);

            throw ValidationException::withMessages([
                'current_profile_id' => '连接检测失败，请检查配置与网络连通性',
            ]);
        }

        $this->settings->enabled = true;
        $this->settings->current_profile_id = $profile->id;
        $this->settings->save();
    }

    public function asController(Request $request)
    {
        $data = StorageSettingData::from($request);
        $this->handle($data);

        return back();
    }
}
