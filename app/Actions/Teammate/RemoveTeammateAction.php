<?php

namespace App\Actions\Teammate;

use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class RemoveTeammateAction
{
    use AsAction;

    public function handle(Workspace $workspace, string $id): void
    {
        $targetUser = $workspace->users()->whereKey($id)->firstOrFail();

        Gate::authorize('workspace-users.removeMember', [$workspace, $targetUser]);

        if (filled($workspace->owner_id) && (string) $workspace->owner_id === (string) $targetUser->id) {
            throw ValidationException::withMessages([
                'user_id' => __('workspace.cannot_remove_owner'),
            ]);
        }

        $workspace->users()->detach($targetUser->id);
    }

    public function asController(Request $request, Workspace $currentWorkspace, string $slug, string $id)
    {
        $this->handle($currentWorkspace, $id);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return back();
    }
}
