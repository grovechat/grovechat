<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AttachmentService;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'image', 'max:2048', 'mimes:jpeg,png,jpg,gif,webp'],
            'folder' => 'string|alpha_dash',
        ]);

        $folder = $request->input('folder', 'uploads');
        $attachment = AttachmentService::upload($request->file('file'), $folder);

        return response()->json([
            'id'   => $attachment->id,
            'path' => $attachment->path,
            'full_url'  => $attachment->full_url,
            'name' => $attachment->file_name,
            'size' => $attachment->file_size,
        ]);
    }
}
