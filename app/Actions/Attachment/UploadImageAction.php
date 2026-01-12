<?php

namespace App\Actions\Attachment;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class UploadImageAction
{
    use AsAction;

    public function handle(UploadedFile $file, string $folder = 'uploads', ?Model $attachable = null)
    {
        Attachment::setFilesystem();
        $fileName = Str::uuid().'.'.$file->getClientOriginalExtension();
        $path = $file->storePubliclyAs($folder, $fileName, config('filesystems.default'));

        return Attachment::create([
            'disk' => config('filesystems.default'),
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
