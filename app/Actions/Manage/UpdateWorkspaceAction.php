<?php

namespace App\Actions\Manage;

use App\Data\CurrentWorkspace\FormUpdateWorkspaceData;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateWorkspaceAction
{
    use AsAction;

    public function handle(Workspace $workspace, FormUpdateWorkspaceData $data)
    {
        $workspace->update($data->toArray());
    }

    public function asController(Request $request, Workspace $currentWorkspace)
    {
        $data = FormUpdateWorkspaceData::from($request);
        $this->handle($currentWorkspace, $data);

        return redirect()->route('get-current-workspace', ['slug' => $currentWorkspace->slug]);
    }
}
