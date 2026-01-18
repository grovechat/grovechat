<?php

namespace App\Actions\User;

use App\Data\Teammate\UserOnlineStatusUpdateData;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateUserOnlineStatusAction
{
    use AsAction;

    public function handle(Workspace $workspace, string $id, UserOnlineStatusUpdateData $data): void
    {
        $user = $workspace->users()->whereKey($id)->firstOrFail();

        $user->forceFill([
            'online_status' => $data->online_status->value,
        ])->save();
    }

    public function asController(Request $request, Workspace $currentWorkspace, string $slug, string $id)
    {
        $data = UserOnlineStatusUpdateData::from($request);
        $this->handle($currentWorkspace, $id, $data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return back();
    }
}
