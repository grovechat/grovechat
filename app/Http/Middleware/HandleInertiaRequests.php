<?php

namespace App\Http\Middleware;

use App\Domain\SystemSettings\DTOs\GeneralSettingsData;
use App\Settings\GeneralSettings;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
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

        $user = $request->user();
        $tenants = null;
        $currentTenant = null;

        if ($user) {
            // 获取用户的所有租户
            $tenants = $user->tenants()->get()->toArray();

            // 获取当前租户（从请求中）
            if ($request->route('tenant_path')) {
                $currentTenant = $user->tenants()
                    ->where('path', $request->route('tenant_path'))
                    ->first();
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
            'generalSettings' => GeneralSettingsData::from(app(GeneralSettings::class)),
            'tenants' => $tenants,
            'currentTenant' => $currentTenant,
        ];
    }
}
