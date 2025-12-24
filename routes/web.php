<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/test', function () {
    return response("hello, world!");
})->withoutMiddleware([
    \App\Http\Middleware\HandleAppearance::class,
    \App\Http\Middleware\HandleLocale::class,
    \App\Http\Middleware\HandleInertiaRequests::class,
]);

Route::get('/', function () {
    return redirect()->to('/login');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
