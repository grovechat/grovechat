<?php

use App\Actions\Attachment\UploadImageAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/upload-image', UploadImageAction::class)->middleware('auth:sanctum')->name('upload-image');
