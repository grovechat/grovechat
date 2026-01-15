<?php

namespace App\Actions\User;

use App\Data\UserUpdateData;
use App\Enums\WorkspaceRole;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateUserAction
{
    use AsAction;

    public function handle(Workspace $workspace, User $user, UserUpdateData $data): void
    {
        DB::transaction(function () use ($workspace, $user, $data) {
            $currentRole = WorkspaceRole::tryFrom((string) ($user->pivot?->role ?? '')) ?? WorkspaceRole::OPERATOR;

            $user->update([
                'name' => $data->name,
                'email' => $data->email,
                'avatar' => $data->avatar,
                'nickname' => $data->nickname,
            ]);

            if (filled($data->password)) {
                Gate::authorize('workspace-users.updatePassword', [$workspace]);
                $user->update([
                    'password' => $data->password,
                ]);
            }

            if ($data->role !== $currentRole) {
                Gate::authorize('workspace-users.updateRole', [$workspace, $user, $data->role]);

                $workspace->users()->updateExistingPivot($user->id, [
                    'role' => $data->role->value,
                ]);
            }
        });
    }

    public function asController(Request $request, Workspace $currentWorkspace, string $slug, string $id)
    {
        $user = $currentWorkspace->users()->whereKey($id)->firstOrFail();

        if ($request->input('password') === '') {
            $request->merge([
                'password' => null,
                'password_confirmation' => null,
            ]);
        }

        $data = UserUpdateData::from($request);
        $this->handle($currentWorkspace, $user, $data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return redirect()->route('show-user-list', ['slug' => $currentWorkspace->slug]);
    }
}
