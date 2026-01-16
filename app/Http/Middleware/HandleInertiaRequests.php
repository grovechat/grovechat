<?php

namespace App\Http\Middleware;

use App\Actions\SystemSetting\GetGeneralSettingAction;
use App\Data\WorkspaceData;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    public function __construct(
        public GetGeneralSettingAction $getGeneralSettingAction
    ) {}

    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        $path = '/'.ltrim($request->path(), '/');

        $isAdminPath = str_starts_with($path, '/admin');
        $isWorkspacePath = str_starts_with($path, '/w/');
        $isSettingsPath = str_starts_with($path, '/settings');

        if ($isWorkspacePath) {
            $user = Auth::guard('web')->user();
        } elseif ($isAdminPath) {
            $user = Auth::guard('admin')->user();
        } elseif ($isSettingsPath) {
            $from = $request->query('from_workspace');
            $hasFromWorkspace = is_string($from) && $from !== '';

            $user = $hasFromWorkspace
                ? Auth::guard('web')->user()
                : (Auth::guard('admin')->user() ?? Auth::guard('web')->user());
        } else {
            $user = Auth::guard('web')->user() ?? Auth::guard('admin')->user();
        }

        $workspaces = collect();
        $currentWorkspace = null;
        $fromWorkspace = null;

        if ($user) {
            /** @var User $user */
            $workspaces = $user->workspaces()->get();

            $routeSlug = $request->route('slug');
            if (is_string($routeSlug) && $routeSlug !== '') {
                $currentWorkspace = $workspaces->firstWhere('slug', $routeSlug);
            }

            $fromSlug = $request->query('from_workspace');
            $fromSlug = is_string($fromSlug) && $fromSlug !== '' ? $fromSlug : null;

            if ($fromSlug) {
                $fromWorkspace = $workspaces->firstWhere('slug', $fromSlug);
            }

            // /settings/* 等无 {slug} 的页面：如果存在 fromWorkspace，则也注入 currentWorkspace
            if (! $currentWorkspace && $fromWorkspace) {
                $currentWorkspace = $fromWorkspace;
            }
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $user,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'generalSettings' => $this->getGeneralSettingAction->run(),
            'workspaces' => $workspaces->map(fn ($w) => WorkspaceData::fromModel($w))->all(),
            'currentWorkspace' => $currentWorkspace ? WorkspaceData::fromModel($currentWorkspace) : null,
            'fromWorkspace' => $fromWorkspace ? WorkspaceData::fromModel($fromWorkspace) : null,
            'fromWorkspaceSlug' => $fromWorkspace?->slug,
        ];
    }
}
