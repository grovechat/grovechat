<?php

namespace App\Actions\Manage;

use App\Exceptions\BusinessException;
use App\Http\RequestContexts\WorkspaceRequestContext;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteCurrentWorkspaceAction
{
    use AsAction;

    public function handle(Workspace $workspace)
    {
        if (! empty($workspace->owner_id)) {
            throw new BusinessException(__('workspace.delete_default_workspace'));
        }

        $workspace->delete();
    }

    public function asController(Request $request)
    {
        $currentWorkspace = WorkspaceRequestContext::fromRequest($request)->workspace;
        $this->handle($currentWorkspace);

        $defaultWorkspace = GetDefaultWorkspaceAction::run($request->user());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return redirect(route('get-current-workspace', $defaultWorkspace->slug));
    }
}
