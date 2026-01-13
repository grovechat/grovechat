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
            abort(403, __('workspace.cannot_delete_own_workspace'));
        }
        $workspace->delete();
    }
    
    public function asController(Request $request, string $slug, string $id)
    {
        $userId = (string) $request->user()?->id;
        $this->handle($id, $userId);

        return redirect()->back();
    }
}
