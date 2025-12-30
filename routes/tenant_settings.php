<?php

/**
 * 管理中心路由
 */

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/**
 * 工作区--常规设置
 */
Route::get('tenant-settings/tenant/general', function () {
    return Inertia::render('tenantSettings/tenant/General');
})->name('tenant-setting.tenant.general');

/**
 * 客服--多客服
 */
Route::get('tenant-settings/teammate/index', function () {
    return Inertia::render('tenantSettings/teammate/Index');
})->name('tenant-setting.teammate.index');

/**
 * 渠道--网站
 */
Route::get('tenant-settings/channels/web', function () {
    return Inertia::render('tenantSettings/channels/Web');
})->name('tenant-setting.channels.web');

/**
 * 数据--标签
 */
Route::get('tenant-settings/datas/tag', function () {
    return Inertia::render('tenantSettings/datas/Tag');
})->name('tenant-setting.datas.tag');

/**
 * 数据--自定义属性
 */
Route::get('tenant-settings/datas/attribute', function () {
    return Inertia::render('tenantSettings/datas/Attribute');
})->name('tenant-setting.datas.attribute');
