<?php

/**
 * 统计相关的路由
 */

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/stats/overview', function () {
    return Inertia::render('stats/Index');
})->name('stats.index');
