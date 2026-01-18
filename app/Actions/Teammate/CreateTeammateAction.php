<?php

namespace App\Actions\Teammate;

use App\Data\Teammate\FormCreateTeammateData;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateTeammateAction
{
    use AsAction;

    public function handle(Workspace $workspace, FormCreateTeammateData $data): User
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
            'nickname' => filled($data->nickname) ? $data->nickname : null,
        ]);

        return $user;
    }

    public function asController(Request $request, Workspace $currentWorkspace)
    {
        $data = FormCreateTeammateData::from($request);
        $this->handle($currentWorkspace, $data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return redirect()->route('show-teammate-list', ['slug' => $currentWorkspace->slug]);
    }
}
