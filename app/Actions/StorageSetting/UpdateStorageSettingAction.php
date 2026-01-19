<?php

namespace App\Actions\StorageSetting;

use App\Data\StorageSetting\FormStorageSettingData;
use App\Models\StorageProfile;
use App\Services\Storage\StorageProfileDisk;
use App\Settings\StorageSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class UpdateStorageSettingAction
{
    use AsAction;

    public function __construct(
        public StorageSettings $settings,
    ) {}

    public function handle(FormStorageSettingData $data): void
    {
        if (! $data->enabled) {
            $this->settings->enabled = false;
            $this->settings->save();

            return;
        }

        if (! filled($data->current_profile_id)) {
            throw ValidationException::withMessages([
                'current_profile_id' => __('storage_settings.storage_not_selected'),
            ]);
        }

        $profile = StorageProfile::query()->find($data->current_profile_id);
        if (! $profile) {
            throw ValidationException::withMessages([
                'current_profile_id' => __('storage_settings.storage_not_found'),
            ]);
        }

        if (! filled($profile->key) || ! filled($profile->secret)) {
            throw ValidationException::withMessages([
                'current_profile_id' => __('storage_settings.storage_key_secret_required'),
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
                'current_profile_id' => __('storage_settings.connection_check_failed'),
            ]);
        }

        $this->settings->enabled = true;
        $this->settings->current_profile_id = $profile->id;
        $this->settings->save();
    }

    public function asController(Request $request)
    {
        $data = FormStorageSettingData::from($request);
        $this->handle($data);
        
        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return back();
    }
}
