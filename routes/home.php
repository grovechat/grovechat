<?php

use App\Http\Middleware\IdentifyTenant;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', IdentifyTenant::class])
    ->prefix('w/{tenant_path}/tenant-settings')
    ->group(function () {


});
