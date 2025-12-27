<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

require __DIR__.'/home.php';
require __DIR__.'/settings.php';
require __DIR__."/system_settiings.php";
require __DIR__.'/tenant_settings.php';
require __DIR__.'/contacts.php';
require __DIR__.'/stats.php';
