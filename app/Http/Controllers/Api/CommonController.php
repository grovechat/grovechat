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
        $request->validate([
            'name' => 'required|string',
            'age' => 'required|integer',
        ]);
        if (!$request->hasFile('file')) {
            throw new BusinessException(__('common.文件上传不能为空'));
        }
        if (!$request->file('file')->isValid()) {
            throw new BusinessException(__('common.无效的文件'));
        }

        $uploaded = $request->file('file');
        if (strpos((string) $uploaded->getMimeType(), 'image/') !== 0) {
            throw new BusinessException(__('common.仅支持图片上传'));
        }

        $path = $request->file('file')->store('uploads', 'public');

        return response()->json(['url' => Storage::url($path)]);
    }
}
