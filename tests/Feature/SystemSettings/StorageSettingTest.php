<?php

use App\Models\StorageProfile;
use App\Models\User;
use App\Settings\StorageSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'is_super_admin' => true,
    ]);
});

test('unauthenticated user cannot view storage settings page', function () {
    $this->get(route('get-storage-setting'))
        ->assertRedirect('/login');
});

test('authenticated user can view storage settings page (new schema)', function () {
    /** @var StorageSettings $settings */
    $settings = app(StorageSettings::class);
    $settings->enabled = true;
    $settings->current_profile_id = null;
    $settings->save();

    $this->actingAs($this->user)
        ->get(route('get-storage-setting'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/storageSetting/Index')
            ->has('storage_settings')
            ->has('storage_profiles')
            ->has('storage_config')
            ->where('storage_settings.enabled', true)
            ->where('storage_settings.current_profile_id', null)
        );
});

test('authenticated user can disable storage (does not clear current_profile_id)', function () {
    /** @var StorageSettings $settings */
    $settings = app(StorageSettings::class);
    $settings->enabled = true;
    $settings->current_profile_id = '01testprofile';
    $settings->save();

    $this->actingAs($this->user)
        ->put(route('update-storage-setting'), [
            'enabled' => false,
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $settings->refresh();
    expect($settings->enabled)->toBeFalse();
    expect($settings->current_profile_id)->toBe('01testprofile');
});

test('enabling storage requires current_profile_id', function () {
    $this->actingAs($this->user)
        ->put(route('update-storage-setting'), [
            'enabled' => true,
            'current_profile_id' => '',
        ])
        ->assertSessionHasErrors('current_profile_id');
});

test('enabling storage fails when selected profile does not exist', function () {
    $this->actingAs($this->user)
        ->put(route('update-storage-setting'), [
            'enabled' => true,
            'current_profile_id' => '01doesnotexist',
        ])
        ->assertSessionHasErrors('current_profile_id');
});

test('enabling storage fails when profile missing credentials', function () {
    $profile = StorageProfile::query()->create([
        'name' => 'p1',
        'provider' => 'tencent',
        'region' => 'ap-guangzhou',
        'endpoint' => 'https://cos.ap-guangzhou.myqcloud.com',
        'bucket' => 'bucket',
        'key' => null,
        'secret' => null,
    ]);

    $this->actingAs($this->user)
        ->put(route('update-storage-setting'), [
            'enabled' => true,
            'current_profile_id' => $profile->id,
        ])
        ->assertSessionHasErrors('current_profile_id');
});

test('authenticated user can enable storage and save settings when profile connection check passes', function () {
    $profile = StorageProfile::query()->create([
        'name' => 'p1',
        'provider' => 'tencent',
        'region' => 'ap-guangzhou',
        'endpoint' => 'https://cos.ap-guangzhou.myqcloud.com',
        'bucket' => 'bucket',
        'key' => 'key',
        'secret' => 'secret',
        'url' => 'https://cdn.example.com',
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

    $this->actingAs($this->user)
        ->put(route('update-storage-setting'), [
            'enabled' => true,
            'current_profile_id' => $profile->id,
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    /** @var StorageSettings $settings */
    $settings = app(StorageSettings::class)->refresh();
    expect($settings->enabled)->toBeTrue();
    expect($settings->current_profile_id)->toBe((string) $profile->id);
});

test('update fails with field error when profile connection check throws', function () {
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
                throw new RuntimeException('connection failed');
            }
        });

    $this->actingAs($this->user)
        ->put(route('update-storage-setting'), [
            'enabled' => true,
            'current_profile_id' => $profile->id,
        ])
        ->assertSessionHasErrors('current_profile_id');
});
