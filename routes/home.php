<?php

use App\Models\Tenant;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function (Tenant $tenant) {
    return redirect()->route('tenant.dashboard', $tenant->path);
});
Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->name('tenant.dashboard');
