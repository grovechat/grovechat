<?php

namespace App\Services;

use App\Enums\StorageProvider;
use Illuminate\Support\Facades\Storage;

class StorageService
{
    /**
     * 创建临时磁盘配置用于测试
     */
    protected function createTempDiskConfig(array $data): array
    {
        return [
            'driver' => 's3',
            'key' => $data['key'],
            'secret' => $data['secret'],
            'region' => $data['region'] ?? 'us-east-1',
            'bucket' => $data['bucket'],
            'url' => $data['url'] ?? null,
            'endpoint' => $data['endpoint'] ?? null,
            'use_path_style_endpoint' => isset($data['provider']) && $data['provider'] === StorageProvider::MINIO->value,
            'throw' => true,
        ];
    }

    /**
     * 测试存储配置
     */
    public function testConnection(array $data): array
    {
        $config = $this->createTempDiskConfig($data);
        $diskName = 'storage_check_' . md5(uniqid('', true));

        // 动态添加磁盘配置
        config(["filesystems.disks.{$diskName}" => $config]);

        try {
            $disk = Storage::disk($diskName);
            
            // 尝试列出文件来测试连接
            $disk->files('/', false);

            return [
                'success' => true,
                'message' => '存储配置验证成功，连接正常',
                'details' => [
                    'provider' => $data['provider'],
                    'bucket' => $data['bucket'],
                    'region' => $data['region'] ?? 'default',
                    'endpoint' => $data['endpoint'] ?? 'default',
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => '存储配置验证失败',
                'error' => $e->getMessage(),
                'details' => [
                    'provider' => $data['provider'],
                    'bucket' => $data['bucket'],
                ],
            ];
        }
    }
}
