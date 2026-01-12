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

    public function asController(Request $request, string $slug, StorageProfile $profile)
    {
        if ($this->settings->current_profile_id === (string) $profile->id) {
            throw ValidationException::withMessages([
                'profile' => '该配置正在被启用，无法删除',
            ]);
        }

        $refCount = Attachment::query()->where('storage_profile_id', $profile->id)->count();
        if ($refCount > 0) {
            throw ValidationException::withMessages([
                'profile' => '该配置已被附件引用，无法删除',
            ]);
        }

        $profile->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => '已删除存储配置',
        ]);

        return back();
    }
}

