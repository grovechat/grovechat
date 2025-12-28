<?php

/**
 * 联系人相关的路由
 */

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/**
 * 联系人列表
 */
Route::get('contacts/{type}/index', function () {
    return Inertia::render('contacts/Index');
})
->whereIn('type', ['all', 'customers', 'leads'])
->name('contact.index');

/**
 * 访客列表
 */
Route::get('contacts/conversations', function () {
    return Inertia::render('contacts/Conversation');
})->name('contact.conversations');
