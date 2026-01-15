<?php

namespace App\Actions\StorageSetting\StorageProfile;

use App\Models\Attachment;
use App\Models\StorageProfile;
use App\Settings\StorageSettings;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteStorageProfileAction
{
    use AsAction;

    public function __construct(
        public StorageSettings $settings,
    ) {}

    public function handle(StorageProfile $profile): void
    {
        if ($this->settings->current_profile_id === (string) $profile->id) {
            throw ValidationException::withMessages([
                'profile' => __('storage_settings.profile_is_active_cannot_delete'),
            ]);
        }

        $refCount = Attachment::query()->where('storage_profile_id', $profile->id)->count();
        if ($refCount > 0) {
            throw ValidationException::withMessages([
                'profile' => __('storage_settings.profile_is_referenced_cannot_delete'),
            ]);
        }

        $profile->delete();
    }

    public function asController(Request $request, StorageProfile $profile)
    {
        $this->handle($profile);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('storage_settings.profile_deleted'),
        ]);

        return back();
    }
}
