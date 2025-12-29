<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommonController extends Controller
{
    public function uploadImage(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['message' => 'File upload cannot be empty'], 400);
        }
        if (!$request->file('file')->isValid()) {
            return response()->json(['message' => 'File upload failed'], 400);
        }

        $uploaded = $request->file('file');
        if (strpos((string) $uploaded->getMimeType(), 'image/') !== 0) {
            return response()->json(['message' => 'Only image upload is supported'], 400);
        }

        $path = $request->file('file')->store('uploads', 'public');

        $url = Storage::url($path);

        return response()->json(['path' => $path, 'url' => $url]);
    }
}
