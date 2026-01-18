<?php

namespace App\Actions\Manage;

use App\Data\CurrentWorkspace\CreateWorkspaceDTO;
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
            $user->workspaces()->attach($workspace->id, ['role' => WorkspaceRole::ADMIN]);

            return $workspace;
        });
    }

    public function asController(Request $request)
    {
        $data = CreateWorkspaceDTO::from($request);
        $newWorkspace = $this->handle($request->user(), $data);

        return redirect(route('get-current-workspace', $newWorkspace->slug));
    }
}
