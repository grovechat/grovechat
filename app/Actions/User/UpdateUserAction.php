<?php

namespace App\Actions\User;

use App\Data\UserUpdateData;
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

    public function handle(Workspace $workspace, $userId, UserUpdateData $data): void
    {
        $targetUser = User::query()->findOrFail($userId);
        $targetUserWorkspace = $targetUser->workspaces()->where('workspace_id', $workspace->id)->firstOrFail();
        
        // 权限检查
        Gate::authorize('workspace-users.updateProfile', [$workspace, $targetUser]);  
        if ($data->email != $targetUser->email) {
            Gate::authorize('workspace-users.updateEmail', [$workspace, $targetUser]);
        }      
        if (!empty($data->password)) {
            Gate::authorize('workspace-users.updatePassword', [$workspace, $targetUser]);
        }
        if ($data->role->value != $targetUserWorkspace->pivot->role) {
            Gate::authorize('workspace-users.updateRole', [$workspace, $targetUser, $data->role]);
        }
        
        // 更新用户资料
        DB::transaction(function () use ($targetUserWorkspace, $targetUser, $data) {
            $targetUser->update([
                'name' => $data->name,
                'email' => $data->email,
                'avatar' => $data->avatar,
                'nickname' => $data->nickname,
            ]);

            if (filled($data->password)) {
                $targetUser->update([
                    'password' => $data->password,
                ]);
            }

            $targetUserWorkspace->pivot->update([
                'role' => $data->role->value,
            ]);
        });
    }

    public function asController(Request $request, Workspace $workspace, string $slug, string $id)
    {
        $data = UserUpdateData::from($request);
        $this->handle($workspace, $id, $data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return redirect()->route('show-user-list', ['slug' => $workspace->slug]);
    }
}
