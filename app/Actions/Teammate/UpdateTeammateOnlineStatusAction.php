<?php

namespace App\Actions\Teammate;

use App\Data\Teammate\FormUpdateTeammateOnlineStatusData;
use App\Http\RequestContexts\WorkspaceRequestContext;
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

        $user->pivot->update([
            'online_status' => $data->online_status->value,
        ]);
    }

    public function asController(Request $request, string $slug, string $id)
    {
        $currentWorkspace = WorkspaceRequestContext::fromRequest($request)->workspace;
        $data = FormUpdateTeammateOnlineStatusData::from($request);
        $this->handle($currentWorkspace, $id, $data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return back();
    }
}
