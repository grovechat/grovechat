<?php

namespace App\Actions\Workspace;

use App\Models\Workspace;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteWorkspaceAction
{
    use AsAction;

    public function handle(string $id, string $currentUserId): void
    {
        $workspace = Workspace::query()->findOrFail($id);
        if ($workspace->owner_id && (string) $workspace->owner_id === (string) $currentUserId) {
            abort(403, '不允许删除自己作为所有者的工作区');
        }
        $workspace->delete();
    }
    
    public function asController(Request $request, string $id)
    {
        $userId = (string) $request->user()?->id;
        $this->handle($id, $userId);

        return redirect()->back();
    }
}
