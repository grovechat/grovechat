<?php

namespace App\Actions\User;

use App\Data\UserUpdateData;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateUserAction
{
    use AsAction;

    public function handle(Workspace $workspace, User $user, UserUpdateData $data): void
    {
        DB::transaction(function () use ($workspace, $user, $data) {
            $user->update([
                'name' => $data->name,
                'email' => $data->email,
                'avatar' => $data->avatar,
                'external_nickname' => $data->external_nickname,
            ]);

            if (filled($data->password)) {
                $user->update([
                    'password' => $data->password,
                ]);
            }

            $workspace->users()->updateExistingPivot($user->id, [
                'role' => $data->role->value,
            ]);
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
