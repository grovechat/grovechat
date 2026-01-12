<?php

namespace App\Actions\Attachment;

use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteAttachmentAction
{
    use AsAction;

    public function handle(Attachment $attachment)
    {
        Storage::disk($attachment->disk)->delete($attachment->path);
        return $attachment->delete();
    }
}
