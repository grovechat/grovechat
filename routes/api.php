<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/upload-image', [\App\Http\Controllers\Api\CommonController::class, 'uploadImage'])->middleware('auth:sanctum');