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

    public function asController(Request $request, string $slug, StorageProfile $profile)
    {
        try {
            StorageProfileDisk::build($profile)->files('/', false);

            Inertia::flash('toast', [
                'type' => 'success',
                'message' => __('storage_settings.connection_check_success'),
            ]);
        } catch (Throwable $e) {
            Log::warning('Storage profile connection check failed', [
                'storage_profile_id' => $profile->id,
                'exception' => $e,
            ]);

            Inertia::flash('toast', [
                'type' => 'warning',
                'message' => __('storage_settings.connection_check_failed'),
            ]);
        }

        return back();
    }
}

