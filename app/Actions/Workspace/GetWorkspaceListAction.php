<?php

namespace App\Actions\Workspace;

use App\Data\SimplePaginationData;
use App\Data\Workspace\ShowWorkspaceListPagePropsData;
use App\Data\Workspace\WorkspaceData;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class GetWorkspaceListAction
{
    use AsAction;

    public function handle(int $page = 1, int $perPage = 10): ShowWorkspaceListPagePropsData
    {
        $perPage = max(1, min($perPage, 50));
        $page = max(1, $page);

        $paginator = Workspace::query()
            ->with([
                'owner' => fn ($query) => $query->withTrashed()->select(['id', 'name', 'email']),
            ])
            ->withCount([
                'users' => fn ($query) => $query->withTrashed(),
            ])
            ->orderByDesc('created_at')
            ->paginate($perPage, ['id', 'name', 'slug', 'created_at', 'owner_id'], 'page', $page);

        $workspaces = $paginator
            ->getCollection()
            ->map(fn (Workspace $w) => WorkspaceData::fromModel($w))
            ->all();

        return new ShowWorkspaceListPagePropsData(
            workspace_list: $workspaces,
            workspace_list_pagination: new SimplePaginationData(
                current_page: $paginator->currentPage(),
                last_page: $paginator->lastPage(),
                per_page: $paginator->perPage(),
                total: $paginator->total(),
            ),
        );
    }

    public function asController(Request $request)
    {
        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 10);

        return Inertia::render('admin/workspace/List', $this->handle($page, $perPage)->toArray());
    }
}
