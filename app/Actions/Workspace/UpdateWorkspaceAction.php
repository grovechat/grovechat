<?php

namespace App\Actions\Workspace;

use App\Data\UpdateWorkspaceDTO;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateWorkspaceAction
{
    use AsAction;

    public function handle(Workspace $workspace, UpdateWorkspaceDTO $data)
    {
        $workspace->update($data->toArray());
    }

    public function asController(Request $request, Workspace $currentWorkspace)
    {
        $data = UpdateWorkspaceDTO::from($request);
        $this->handle($currentWorkspace, $data);

        return redirect(route('workspace-setting.workspace.general', $currentWorkspace->slug));
    }
}
