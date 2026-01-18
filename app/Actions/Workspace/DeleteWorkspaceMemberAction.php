<?php

namespace App\Actions\Workspace;

use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteWorkspaceMemberAction
{
    use AsAction;

    public function handle(Workspace $workspace, string $userId): void
    {
        if (filled($workspace->owner_id) && (string) $workspace->owner_id === (string) $userId) {
            throw ValidationException::withMessages([
                'user_id' => __('workspace.cannot_remove_owner'),
            ]);
        }

        $workspace->users()->detach($userId);
    }

    public function asController(Request $request, string $id, string $userId): RedirectResponse
    {
        $workspace = Workspace::query()->findOrFail($id);

        $this->handle($workspace, $userId);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return redirect()->route('admin.show-workspace-detail', ['id' => $workspace->id]);
    }
}
