<?php

namespace App\Actions\Teammate;

use App\Data\Teammate\FormCreateTeammateData;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateTeammateAction
{
    use AsAction;

    public function handle(Workspace $workspace, FormCreateTeammateData $data): User
    {
        return DB::transaction(function () use ($workspace, $data) {
            $user = User::query()->create([
                'name' => $data->name,
                'email' => $data->email,
                'avatar' => $data->avatar,
                'nickname' => $data->nickname,
                'password' => $data->password,
            ]);

            $workspace->users()->attach($user->id, [
                'role' => $data->role->value,
            ]);

            return $user;
        });
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
