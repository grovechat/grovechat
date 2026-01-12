<?php

namespace App\Actions\Attachment;

use App\Models\Attachment;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteAttachmentAction
{
    use AsAction;

    public function handle(Attachment $attachment)
    {
        $attachment->filesystem()->delete($attachment->path);
        return $attachment->delete();
    }
}
