<?php

namespace App\Actions\Manage;

use App\Data\CurrentWorkspace\FormCreateWorkspaceData;
use App\Enums\WorkspaceRole;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateWorkspaceAction
{
    use AsAction;

    public function handle(User $user, FormCreateWorkspaceData $data)
    {
        return DB::transaction(function () use ($user, $data) {
            $workspace = Workspace::query()->create(array_merge($data->toArray(), [
                'owner_id' => $user->id,
            ]));
            $user->workspaces()->attach($workspace->id, ['role' => WorkspaceRole::OWNER]);

            return $workspace;
        });
    }

    public function asController(Request $request)
    {
        $data = FormCreateWorkspaceData::from($request);
        $newWorkspace = $this->handle($request->user(), $data);

        return redirect(route('get-current-workspace', $newWorkspace->slug));
    }
}
