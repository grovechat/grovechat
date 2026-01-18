<?php

namespace App\Actions\Manage;

use App\Data\CurrentWorkspace\UpdateWorkspaceData;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateWorkspaceAction
{
    use AsAction;

    public function handle(Workspace $workspace, UpdateWorkspaceData $data)
    {
        $workspace->update($data->toArray());
    }

    public function asController(Request $request, Workspace $currentWorkspace)
    {
        $data = UpdateWorkspaceData::from($request);
        $this->handle($currentWorkspace, $data);

        return redirect()->route('get-current-workspace', ['slug' => $currentWorkspace->slug]);
    }
}
