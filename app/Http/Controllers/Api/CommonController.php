<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommonController extends Controller
{
    public function uploadImage(Request $request)
    {
        if (!$request->hasFile('file')) {
            throw new BusinessException('File upload cannot be empty');
        }
        if (!$request->file('file')->isValid()) {
            throw new BusinessException('File upload failed');
        }

        $uploaded = $request->file('file');
        if (strpos((string) $uploaded->getMimeType(), 'image/') !== 0) {
            throw new BusinessException("Only image upload is supported");
        }

        $path = $request->file('file')->store('uploads', 'public');

        return response()->json(['url' => Storage::url($path)]);
    }
}
