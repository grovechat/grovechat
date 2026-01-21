<?php

use App\Models\Attachment;
use App\Models\StorageProfile;
use App\Models\User;
use App\Settings\StorageSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'is_super_admin' => true,
    ]);
});

test('authenticated user can create storage profile when connection check passes', function () {
    Storage::shouldReceive('build')
        ->once()
        ->andReturn(new class
        {
            public function files($directory = '/', $recursive = false)
            {
                return [];
            }
        });

    $this->actingAs($this->user, 'admin')
        ->post(route('admin.storage-profile.create'), [
            'name' => 'tencent-prod',
            'provider' => 'tencent',
            'region' => 'ap-guangzhou',
            'endpoint' => 'https://cos.ap-guangzhou.myqcloud.com',
            'bucket' => 'bucket',
            'key' => 'key',
            'secret' => 'secret',
            'url' => 'https://cdn.example.com',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $profile = StorageProfile::query()->firstOrFail();
    expect($profile->name)->toBe('tencent-prod');
    expect($profile->provider)->toBe('tencent');
    expect($profile->region)->toBe('ap-guangzhou');
    expect($profile->endpoint)->toBe('https://cos.ap-guangzhou.myqcloud.com');
    expect($profile->bucket)->toBe('bucket');
    expect($profile->key)->toBe('key');
    expect($profile->secret)->toBe('secret');
    expect($profile->url)->toBe('https://cdn.example.com');
});

test('create storage profile fails when provider is invalid enum value', function () {
    Storage::shouldReceive('build')->never();

    $this->actingAs($this->user, 'admin')
        ->post(route('admin.storage-profile.create'), [
            'name' => 'bad-provider',
            'provider' => 'not-a-provider',
            'region' => 'ap-guangzhou',
            'endpoint' => 'https://cos.ap-guangzhou.myqcloud.com',
            'bucket' => 'bucket',
            'key' => 'key',
            'secret' => 'secret',
            'url' => 'https://cdn.example.com',
        ])
        ->assertSessionHasErrors('provider');
});

test('create storage profile fails with field error when connection check fails', function () {
    Storage::shouldReceive('build')
        ->once()
        ->andReturn(new class
        {
            public function files($directory = '/', $recursive = false)
            {
                throw new RuntimeException('connection failed');
            }
        });

    $this->actingAs($this->user, 'admin')
        ->post(route('admin.storage-profile.create'), [
            'name' => 'tencent-prod',
            'provider' => 'tencent',
            'region' => 'ap-guangzhou',
            'endpoint' => 'https://cos.ap-guangzhou.myqcloud.com',
            'bucket' => 'bucket',
            'key' => 'key',
            'secret' => 'secret',
            'url' => 'https://cdn.example.com',
        ])
        ->assertSessionHasErrors('secret');
});

test('authenticated user can update profile name/url without changing credentials', function () {
    $profile = StorageProfile::query()->create([
        'name' => 'p1',
        'provider' => 'tencent',
        'region' => 'ap-guangzhou',
        'endpoint' => 'https://cos.ap-guangzhou.myqcloud.com',
        'bucket' => 'bucket',
        'key' => 'key',
        'secret' => 'secret',
        'url' => null,
    ]);

    Storage::shouldReceive('build')->never();

    $this->actingAs($this->user, 'admin')
        ->put(route('admin.storage-profile.update', ['profile' => $profile->id]), [
            'name' => 'p1-new',
            'url' => 'https://cdn.example.com',
            'key' => '',
            'secret' => '',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $profile->refresh();
    expect($profile->name)->toBe('p1-new');
    expect($profile->url)->toBe('https://cdn.example.com');
    expect($profile->key)->toBe('key');
    expect($profile->secret)->toBe('secret');
});

test('updating credentials requires both key and secret', function () {
    $profile = StorageProfile::query()->create([
        'name' => 'p1',
        'provider' => 'tencent',
        'region' => 'ap-guangzhou',
        'endpoint' => 'https://cos.ap-guangzhou.myqcloud.com',
        'bucket' => 'bucket',
        'key' => 'key',
        'secret' => 'secret',
    ]);

    Storage::shouldReceive('build')->never();

    $this->actingAs($this->user, 'admin')
        ->put(route('admin.storage-profile.update', ['profile' => $profile->id]), [
            'name' => 'p1',
            'url' => '',
            'key' => 'new-key',
            'secret' => '',
        ])
        ->assertSessionHasErrors('secret');
});

test('authenticated user can update credentials when connection check passes', function () {
    $profile = StorageProfile::query()->create([
        'name' => 'p1',
        'provider' => 'tencent',
        'region' => 'ap-guangzhou',
        'endpoint' => 'https://cos.ap-guangzhou.myqcloud.com',
        'bucket' => 'bucket',
        'key' => 'key',
        'secret' => 'secret',
    ]);

    Storage::shouldReceive('build')
        ->once()
        ->andReturn(new class
        {
            public function files($directory = '/', $recursive = false)
            {
                return [];
            }
        });

    $this->actingAs($this->user, 'admin')
        ->put(route('admin.storage-profile.update', ['profile' => $profile->id]), [
            'name' => 'p1',
            'url' => '',
            'key' => 'new-key',
            'secret' => 'new-secret',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $profile->refresh();
    expect($profile->key)->toBe('new-key');
    expect($profile->secret)->toBe('new-secret');
});

test('authenticated user can check storage profile connection', function () {
    $profile = StorageProfile::query()->create([
        'name' => 'p1',
        'provider' => 'tencent',
        'region' => 'ap-guangzhou',
        'endpoint' => 'https://cos.ap-guangzhou.myqcloud.com',
        'bucket' => 'bucket',
        'key' => 'key',
        'secret' => 'secret',
    ]);

    Storage::shouldReceive('build')
        ->once()
        ->andReturn(new class
        {
            public function files($directory = '/', $recursive = false)
            {
                return [];
            }
        });

    $this->actingAs($this->user, 'admin')
        ->put(route('admin.storage-profile.check', ['profile' => $profile->id]))
        ->assertRedirect();
});

test('cannot delete currently selected profile', function () {
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

    $this->actingAs($this->user, 'admin')
        ->delete(route('admin.storage-profile.delete', ['profile' => $profile->id]))
        ->assertSessionHasErrors('profile');
});

test('cannot delete profile that is referenced by attachments', function () {
    if (! Schema::hasColumn('attachments', 'storage_profile_id')) {
        $this->markTestSkipped('attachments.storage_profile_id 不存在，跳过引用校验测试');
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

    Attachment::query()->create([
        'disk' => 's3',
        'storage_profile_id' => $profile->id,
        'path' => 'uploads/a.png',
        'file_name' => 'a.png',
        'file_type' => 'image/png',
        'file_size' => 123,
        'attachable_id' => null,
        'attachable_type' => null,
    ]);

    $this->actingAs($this->user, 'admin')
        ->delete(route('admin.storage-profile.delete', ['profile' => $profile->id]))
        ->assertSessionHasErrors('profile');
});
