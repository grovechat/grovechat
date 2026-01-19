<?php

namespace App\Actions\Workspace;

use App\Data\Workspace\FormAddWorkspaceMemberData;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class AddWorkspaceMemberAction
{
    use AsAction;

    public function handle(Workspace $workspace, FormAddWorkspaceMemberData $data): void
    {
        $user = User::query()
            ->where('is_super_admin', false)
            ->findOrFail($data->user_id);

        if (filled($workspace->owner_id) && (string) $workspace->owner_id === (string) $user->id) {
            throw ValidationException::withMessages([
                'user_id' => __('workspace.cannot_select_owner'),
            ]);
        }

        if ($workspace->users()->whereKey($user->id)->exists()) {
            throw ValidationException::withMessages([
                'user_id' => __('workspace.user_already_in_workspace'),
            ]);
        }

        $workspace->users()->attach($user->id, [
            'role' => $data->role->value,
        ]);
    }

    public function asController(Request $request, string $id): RedirectResponse
    {
        $workspace = Workspace::query()->findOrFail($id);
        $data = FormAddWorkspaceMemberData::from($request);

        $this->handle($workspace, $data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return redirect()->route('admin.show-workspace-detail', ['id' => $workspace->id]);
    }
}
