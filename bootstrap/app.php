<?php

use App\Exceptions\BusinessException;
use App\Http\Middleware\CheckSuperAdmin;
use App\Http\Middleware\EnsureSettingsWorkspace;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\HandleLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state', 'locale']);

        $middleware->alias([
            'is_super_admin' => CheckSuperAdmin::class,
            'ensure_settings_workspace' => EnsureSettingsWorkspace::class,
        ]);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleLocale::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->api(append: [
            HandleLocale::class,
        ]);

        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, Throwable $exception, \Illuminate\Http\Request $request) {
            // 处理业务异常
            if ($exception instanceof BusinessException) {
                if ($request->header('X-Inertia')) {
                    return back()->withErrors(['toast' => $exception->getMessage()]);
                } else {
                    return response()->json(['message' => $exception->getMessage()], 422);
                }
            }

            // 处理验证异常 - 优化 API 的异常响应格式
            if ($exception instanceof \Illuminate\Validation\ValidationException) {
                if ($request->expectsJson() && ! $request->header('X-Inertia')) {
                    $errors = $exception->errors();
                    $firstField = array_key_first($errors);
                    $firstError = $errors[$firstField][0];

                    return response()->json([
                        'message' => $firstField.' '.$firstError,
                        'errors' => $errors,
                    ], 422)->setEncodingOptions(JSON_UNESCAPED_UNICODE);
                }
            }

            return $response;
        });
    })->create()
    ->useStoragePath(env('LARAVEL_STORAGE_PATH', base_path('storage')));
