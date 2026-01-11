<?php

namespace App\Actions\Workspace;

use App\Data\CreateWorkspaceDTO;
use App\Enums\WorkspaceRole;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateWorkspaceAction
{
    use AsAction;

    public function handle(User $user, CreateWorkspaceDTO $dto)
    {
        return DB::transaction(function () use ($user, $dto) {
            $workspace = Workspace::query()->create($dto->toArray());
            $workspace->createSlug();
            $user->workspaces()->attach($workspace->id, ['role' => WorkspaceRole::ADMIN]);

            return $workspace;
        });
    }

    public function asController(Request $request)
    {
        $data = CreateWorkspaceDTO::from($request->all());
        $newWorkspace = $this->handle($request->user(), $data);

        return redirect(route('workspace-setting.workspace.general', $newWorkspace->slug));
    }
}
