<?php

namespace App\Services;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentService
{
    /**
     * 上传附件并记录到数据库
     */
    public static function upload(
        UploadedFile $file,
        string $folder = 'uploads',
        ?Model $attachable = null
    ): Attachment {
        $disk = config('filesystems.default');
        $fileName = Str::uuid().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $fileName, $disk);

        return Attachment::create([
            'disk' => $disk,
            'path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'attachable_id' => $attachable?->id,
            'attachable_type' => $attachable ? get_class($attachable) : null,
        ]);
    }

    /**
     * 把附件绑定到指定模型
     */
    public static function bind(string $id, Model $attachable)
    {
        $attachment = Attachment::query()->findOrFail($id);
        if (! empty($attachable->attachable_id)) {
            Storage::disk($attachment->disk)->delete($attachment->path);
        }
        $attachment->update([
            'attachable_id' => $attachable->id,
            'attachable_type' => $attachable->getMorphClass(),
        ]);
    }

    /**
     * 更新附件模型
     */
    public static function replace(string $oldId, string $newId, Model $attachable)
    {
        self::delete($oldId);

        self::bind($newId, $attachable);
    }

    /**
     * 删除附件
     */
    public static function delete($attachmentId): bool
    {
        $attachment = Attachment::find($attachmentId);

        if ($attachment) {
            Storage::disk($attachment->disk)->delete($attachment->path);

            return $attachment->delete();
        }

        return false;
    }
}
