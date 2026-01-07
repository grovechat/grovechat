<?php

use App\Exceptions\BusinessException;
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

        $middleware->web(append: [
            HandleAppearance::class,
            HandleLocale::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
        
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, Throwable $exception, \Illuminate\Http\Request $request) {
            if ($exception instanceof BusinessException) {
                dd($exception->getMessage());
                if ($request->header('X-Inertia')) {
                    return back()->withErrors(['toast' => $exception->getMessage()]);
                } else {
                    return response()->json(['message' => $exception->getMessage()], 422);
                }
            }
            return $response;
        });
    })->create()
    ->useStoragePath(env('LARAVEL_STORAGE_PATH', base_path('storage')));
