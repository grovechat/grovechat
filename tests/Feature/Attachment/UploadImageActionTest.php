<?php

use App\Actions\Attachment\UploadImageAction;
use App\Models\StorageProfile;
use App\Settings\StorageSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\WithWorkspace;

uses(RefreshDatabase::class, WithWorkspace::class);

beforeEach(function () {
    $this->user = $this->createUserWithWorkspace();
});

test('upload uses public disk when storage is disabled', function () {
    Storage::fake('public');

    /** @var StorageSettings $settings */
    $settings = app(StorageSettings::class);
    $settings->enabled = false;
    $settings->current_profile_id = null;
    $settings->save();

    $file = UploadedFile::fake()->image('a.png');

    $attachment = UploadImageAction::run($file, 'uploads');

    expect($attachment->disk)->toBe('public');
    Storage::disk('public')->assertExists($attachment->path);
});

test('upload uses current storage profile when enabled', function () {
    if (! Schema::hasColumn('attachments', 'storage_profile_id')) {
        $this->markTestSkipped('attachments.storage_profile_id 不存在，跳过对象存储上传测试');
    }

    $profile = StorageProfile::query()->create([
        'name' => 'p1',
        'provider' => 'tencent',
        'region' => 'ap-guangzhou',
        'endpoint' => 'https://cos.ap-guangzhou.myqcloud.com',
        'bucket' => 'bucket',
        'key' => 'key',
        'secret' => 'secret',
    ]);

    /** @var StorageSettings $settings */
    $settings = app(StorageSettings::class);
    $settings->enabled = true;
    $settings->current_profile_id = (string) $profile->id;
    $settings->save();

    $fakeDisk = new class
    {
        public array $puts = [];

        public function putFileAs($path, $file, $name, $options = [])
        {
            $this->puts[] = compact('path', 'name', 'options');
            return trim($path, '/').'/'.$name;
        }
    };

    Storage::shouldReceive('build')
        ->once()
        ->andReturn($fakeDisk);

    $file = UploadedFile::fake()->image('a.png');

    $attachment = UploadImageAction::run($file, 'uploads');

    expect($attachment->disk)->toBe('s3');
    expect($attachment->storage_profile_id)->toBe((string) $profile->id);
    expect($attachment->path)->toStartWith('uploads/');
});

