<?php

use App\Http\Middleware\IdentifyTenant;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

// 自动重定向到用户的第一个租户
Route::get('dashboard', function () {
    $user = auth()->user();
    if ($user) {
        $firstTenant = $user->tenants()->first();
        if ($firstTenant) {
            return redirect()->route('tenant.dashboard', ['tenant_path' => $firstTenant->path]);
        }
    }
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', IdentifyTenant::class])
    ->prefix('w/{tenant_path}')
    ->group(function () {
        require __DIR__.'/home.php';
        require __DIR__.'/settings.php';
        require __DIR__."/system_settiings.php";
        require __DIR__.'/tenant_settings.php';
        require __DIR__.'/contacts.php';
        require __DIR__.'/stats.php';
    });
