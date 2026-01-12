<?php

namespace App\Actions\Attachment;

use App\Models\Attachment;
use App\Models\StorageProfile;
use App\Services\Storage\StorageProfileDisk;
use App\Settings\StorageSettings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class UploadImageAction
{
    use AsAction;

    public function handle(UploadedFile $file, string $folder = 'uploads', ?Model $attachable = null)
    {
        $fileName = Str::uuid().'.'.$file->getClientOriginalExtension();

        /** @var StorageSettings $settings */
        $settings = app(StorageSettings::class);

        $diskName = 'public';
        $storageProfileId = null;

        if ($settings->enabled) {
            if (! filled($settings->current_profile_id)) {
                throw ValidationException::withMessages([
                    'storage' => '对象存储已启用，但未选择存储配置',
                ]);
            }

            $profile = StorageProfile::query()->find($settings->current_profile_id);
            if (! $profile) {
                throw ValidationException::withMessages([
                    'storage' => '当前存储配置不存在，请重新选择',
                ]);
            }

            $disk = StorageProfileDisk::build($profile);
            $path = $disk->putFileAs($folder, $file, $fileName, ['visibility' => 'public']);

            $diskName = 's3';
            $storageProfileId = $profile->id;
        } else {
            $path = $file->storePubliclyAs($folder, $fileName, 'public');
        }

        return Attachment::create([
            'disk' => $diskName,
            'storage_profile_id' => $storageProfileId,
            'path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'attachable_id' => $attachable?->id,
            'attachable_type' => $attachable ? get_class($attachable) : null,
        ]);
        
    }
    
    public function asController(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'image', 'max:2048', 'mimes:jpeg,png,jpg,gif,webp'],
            'folder' => 'string|alpha_dash',
        ]);
        
        return $this->handle($request->file('file'), $request->input('folder', 'uploads'));
    }
}
