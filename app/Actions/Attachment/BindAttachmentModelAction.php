<?php

namespace App\Actions\Attachment;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Lorisleiva\Actions\Concerns\AsAction;

class BindAttachmentModelAction
{
    use AsAction;

    public function handle(Attachment $attachment, Model $attachable)
    {
        if (! empty($attachable->attachable_id)) {
            $attachment->filesystem()->delete($attachment->path);
        }
        
        $attachment->update([
            'attachable_id' => $attachable->id,
            'attachable_type' => $attachable->getMorphClass(),
        ]);
    }
}
