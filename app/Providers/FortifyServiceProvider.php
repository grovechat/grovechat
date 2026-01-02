<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 忽略 Fortify 的默认路由，使用我们自己的 DDD 架构路由
        Fortify::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureRateLimiting();
    }

    /**
     * Configure Fortify actions - 使用 DDD Actions (通过 Adapter 桥接)
     */
    private function configureActions(): void
    {
        // 注册用户创建 Action (使用 Adapter 桥接到 DDD Actions)
        Fortify::createUsersUsing(\App\Actions\Fortify\CreateNewUserAdapter::class);

        // 密码重置 Action (使用 Adapter 桥接到 DDD Actions)
        Fortify::resetUserPasswordsUsing(\App\Actions\Fortify\ResetUserPasswordAdapter::class);
    }

    /**
     * Configure rate limiting.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
