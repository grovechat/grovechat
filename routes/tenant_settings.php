<?php

/**
 * 管理中心路由
 */

use App\Http\Middleware\IdentifyTenant;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', IdentifyTenant::class])
    ->prefix('w/{tenant_path}/tenant-settings')
    ->group(function () {

    Route::get('/test', function () {
        return response("test");
    });
});
