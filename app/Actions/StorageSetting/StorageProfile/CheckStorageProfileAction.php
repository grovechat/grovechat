<?php

namespace App\Actions\StorageSetting\StorageProfile;

use App\Models\StorageProfile;
use App\Services\Storage\StorageProfileDisk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class CheckStorageProfileAction
{
    use AsAction;

    public function handle(StorageProfile $profile): bool
    {
        try {
            StorageProfileDisk::build($profile)->files('/', false);

            return true;
        } catch (Throwable $e) {
            Log::warning('Storage profile connection check failed', [
                'storage_profile_id' => $profile->id,
                'exception' => $e,
            ]);

            return false;
        }
    }

    public function asController(Request $request, StorageProfile $profile)
    {
        $ok = $this->handle($profile);

        Inertia::flash('toast', [
            'type' => $ok ? 'success' : 'warning',
            'message' => $ok
                ? __('storage_settings.connection_check_success')
                : __('storage_settings.connection_check_failed'),
        ]);

        return back();
    }
}
