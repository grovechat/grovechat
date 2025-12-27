<?php

use App\Http\Middleware\IdentifyTenant;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// 全局 dashboard - 自动重定向到用户的第一个租户
Route::get('dashboard', function () {
    $user = auth()->user();
    
    if ($user) {
        $firstTenant = $user->tenants()->first();
        
        if ($firstTenant) {
            return redirect()->route('tenant.dashboard', ['tenant_path' => $firstTenant->path]);
        }
    }
    
    // 如果没有租户，返回首页
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

// 租户 dashboard
Route::middleware(['auth', 'verified', IdentifyTenant::class])
    ->prefix('w/{tenant_path}')
    ->group(function () {
        Route::get('dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('tenant.dashboard');
    });
