<?php

namespace App\Actions\Teammate;

use App\Data\Teammate\FormUpdateTeammateOnlineStatusData;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateTeammateOnlineStatusAction
{
    use AsAction;

    public function handle(Workspace $workspace, string $id, FormUpdateTeammateOnlineStatusData $data): void
    {
        $user = $workspace->users()->whereKey($id)->firstOrFail();

        $user->forceFill([
            'online_status' => $data->online_status->value,
        ])->save();
    }

    public function asController(Request $request, Workspace $currentWorkspace, string $slug, string $id)
    {
        $data = FormUpdateTeammateOnlineStatusData::from($request);
        $this->handle($currentWorkspace, $id, $data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return back();
    }
}
