<?php

namespace App\Actions\Workspace;

use App\Data\SimplePaginationData;
use App\Data\CurrentWorkspace\WorkspaceDetailData;
use App\Data\CurrentWorkspace\WorkspaceDetailPagePropsData;
use App\Data\CurrentWorkspace\WorkspaceMemberData;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowWorkspaceDetailAction
{
    use AsAction;

    public function handle(string $id, int $page = 1, int $perPage = 10)
    {
        $perPage = max(1, min($perPage, 50));
        $page = max(1, $page);

        $workspace = Workspace::query()
            ->with([
                'owner' => fn ($query) => $query->withTrashed()->select(['id', 'name', 'email']),
            ])
            ->withCount([
                'users' => fn ($query) => $query->withTrashed(),
            ])
            ->findOrFail($id);

        $paginator = $workspace->users()
            ->withTrashed()
            ->orderByPivot('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $members = $paginator
            ->getCollection()
            ->map(fn (User $u) => WorkspaceMemberData::fromModel($u))
            ->all();

        return new WorkspaceDetailPagePropsData(
            workspace_detail: WorkspaceDetailData::fromModel($workspace),
            workspace_members: $members,
            workspace_members_pagination: new SimplePaginationData(
                current_page: $paginator->currentPage(),
                last_page: $paginator->lastPage(),
                per_page: $paginator->perPage(),
                total: $paginator->total(),
            ),
        );
    }

    public function asController(Request $request, string $id)
    {
        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 10);

        return Inertia::render('admin/workspace/Show', $this->handle($id, $page, $perPage)->toArray());
    }
}
