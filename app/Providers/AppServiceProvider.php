<?php

namespace App\Providers;

use App\Enums\StorageProvider;
use App\Settings\StorageSettings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->runningInConsole() || !Schema::hasTable('settings')) {
            return;
        }

        try {
            $settings = app(StorageSettings::class);
            $this->injectStorageConfig($settings);
        } catch (\Exception $e) {
            Log::warning("Storage settings could not be loaded: " . $e->getMessage());
        }        
    }
    
    protected function injectStorageConfig(StorageSettings $settings): void
    {
        if (!$settings->enabled) {
            config(['filesystems.default' => 'public']);
            return;
        }

        config([
            'filesystems.default' => 's3',
            'filesystems.disks.s3' => [
                'driver' => 's3',
                'key'    => $settings->key,
                'secret' => $settings->secret,
                'region' => $settings->region ?? 'us-east-1',
                'bucket' => $settings->bucket,
                'endpoint' => $settings->endpoint,
                'url'    => $settings->url,
                'use_path_style_endpoint' => $settings->provider == StorageProvider::MINIO,
            ],
        ]);
    }
}
