<?php

namespace App\Actions\Workspace;

use App\Exceptions\BusinessException;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class DestroyWorkspaceAction
{
    use AsAction;

    public function handle(Workspace $workspace)
    {
        if (!empty($workspace->owner_id)) {
            throw new BusinessException("不能删除默认工作区");
        }

        $workspace->delete();
    }
    
    public function asController(Request $request, Workspace $currentWorkspace)
    {
        $this->handle($currentWorkspace);
        
        $defaultWorkspace = GetDefaultWorkspaceAction::run($request->user());
        
        Inertia::flash('toast', [
            'type' => 'success',
            'message' => '工作区已删除'
        ]);
        
        return redirect(route('workspace-setting.workspace.general', $defaultWorkspace->slug));
    }
}
