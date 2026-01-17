<?php

namespace App\Actions\User;

use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteUserAction
{
    use AsAction;

    public function handle(Workspace $workspace, string $id): void
    {
        $targetUser = $workspace->users()->whereKey($id)->firstOrFail();
        
        Gate::authorize('workspace-users.deleteUser', [$workspace, $targetUser]);
        
        $targetUser->delete();
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
