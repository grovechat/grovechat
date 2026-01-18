<?php

namespace App\Actions\SystemSetting\User;

use App\Data\SimplePaginationData;
use App\Data\SystemUserListItemData;
use App\Data\SystemUserListPagePropsData;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowSystemUserListAction
{
    use AsAction;

    public function handle(int $page = 1, int $perPage = 10): SystemUserListPagePropsData
    {
        $perPage = max(1, min($perPage, 50));
        $page = max(1, $page);

        $paginator = User::query()
            ->where('is_super_admin', false)
            ->orderBy('id')
            ->paginate($perPage, ['id', 'name', 'email', 'avatar', 'two_factor_confirmed_at'], 'page', $page);

        $users = $paginator->getCollection()
            ->map(fn (User $user) => SystemUserListItemData::fromModel($user))
            ->all();

        return new SystemUserListPagePropsData(
            user_list: $users,
            user_list_pagination: new SimplePaginationData(
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

        return Inertia::render('admin/user/List', $this->handle($page, $perPage)->toArray());
    }
}
