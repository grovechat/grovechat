<?php

namespace App\Actions\Attachment;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

class BindAttachmentModelAction
{
    use AsAction;

    public function handle(Attachment $attachment, Model $attachable)
    {
        if (! empty($attachable->attachable_id)) {
            Storage::disk($attachment->disk)->delete($attachment->path);
        }
        
        $attachment->update([
            'attachable_id' => $attachable->id,
            'attachable_type' => $attachable->getMorphClass(),
        ]);
    }
}
