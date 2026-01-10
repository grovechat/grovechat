<?php

namespace App\Actions\StorageSetting;

use App\Data\StorageSettingData;
use App\Services\StorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class CheckStorageSettingAction
{
    use AsAction;

    public function __construct(
        protected StorageService $storageService
    ) {}

    public function handle(StorageSettingData $data)
    {
        $config = [
            'driver' => 's3',
            'key' => $data->key,
            'secret' => $data->secret,
            'region' => $data->region,
            'bucket' => $data->bucket,
            'url' => $data->url,
            'endpoint' => $data->endpoint,
            'throw' => true,
        ];
        $diskName = 'storage_check_' . md5(uniqid('', true));
        config(["filesystems.disks.{$diskName}" => $config]);
        
        Storage::disk($diskName)->files('/', false);
    }
    
    public function asController(Request $request)
    {
        $data = StorageSettingData::from($request->all());
        
        try {
            $result = $this->handle($data); 
            Inertia::flash('toast', [
                'type' => 'success',
                'message' => '检测成功'
            ]);
        } catch (Throwable $e) {
            Inertia::flash('toast', [
                'type' => 'warning',
                'message' => $e->getMessage(),
            ]);
        }
        
        return back();
    }
}
