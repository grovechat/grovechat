<?php

namespace App\Http\Controllers;

use App\Data\CreateWorkspaceDTO;
use App\Data\UpdateWorkspaceDTO;
use App\Enums\WorkspaceRole;
use App\Exceptions\BusinessException;
use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class WorkspaceSettingController extends Controller
{
    public function __construct(
        public Workspace $workspace
    ) { }

    public function showWorkspaceGeneralPage()
    {
        return Inertia::render("workspaceSettings/workspace/General", [
            'workspace' => $this->workspace,
        ]);
    }

    public function showCreateWorkspacePage()
    {
        return Inertia::render('workspaceSettings/workspace/CreateWorkspace');
    }

    public function updateWorkspace(UpdateWorkspaceDTO $dto)
    {
        $this->workspace->update($dto->toArray());

        return redirect(route('workspace-setting.workspace.general', $this->workspace->slug));
    }

    public function storeWorkspace(CreateWorkspaceDTO $dto)
    {
        $newWorkspace = DB::transaction(function () use ($dto) {
            $workspace = Workspace::query()->create($dto->toArray());
            $workspace->createSlug();

            /** @var User $user */
            $user = Auth::user();
            $user->workspaces()->attach($workspace->id, ['role' => WorkspaceRole::ADMIN]);

            return $workspace;
        });

        return redirect(route('workspace-setting.workspace.general', $newWorkspace->slug));
    }

    public function deleteWorkspace()
    {
        if (!empty($this->workspace->owner_id)) {
            throw new BusinessException("不能删除默认工作区");
        }

        $this->workspace->delete();

        $defaultWorkspace = Workspace::query()->where('owner_id', Auth::id())->firstOrFail();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => '工作区已删除'
        ]);
        return redirect(route('workspace-setting.workspace.general', $defaultWorkspace->slug));
    }
}
