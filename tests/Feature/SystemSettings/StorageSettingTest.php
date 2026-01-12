<?php

use App\Enums\StorageProvider;
use App\Settings\StorageSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\WithWorkspace;

uses(RefreshDatabase::class, WithWorkspace::class);

beforeEach(function () {
    $this->user = $this->createUserWithWorkspace();
});

test('unauthenticated user cannot view storage settings page', function () {
    $this->get(route('system-setting.get-storage-settings', ['slug' => $this->workspaceSlug()]))
        ->assertRedirect('/login');
});

test('authenticated user can view storage settings page and secret is not returned', function () {
    /** @var StorageSettings $settings */
    $settings = app(StorageSettings::class);
    $settings->enabled = true;
    $settings->provider = StorageProvider::AWS->value;
    $settings->key = 'key';
    $settings->secret = 'super-secret';
    $settings->bucket = 'bucket';
    $settings->region = 'us-east-1';
    $settings->endpoint = 'https://s3.us-east-1.amazonaws.com';
    $settings->url = 'https://cdn.example.com';
    $settings->save();

    $this->actingAs($this->user)
        ->get(route('system-setting.get-storage-settings', ['slug' => $this->workspaceSlug()]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('systemSettings/StorageSetting')
            ->has('storage_settings')
            ->has('storage_config')
            ->where('storage_settings.enabled', true)
            ->where('storage_settings.provider', StorageProvider::AWS->value)
            ->where('storage_settings.secret', null)
        );
});

test('authenticated user can disable storage without overwriting existing config', function () {
    /** @var StorageSettings $settings */
    $settings = app(StorageSettings::class);
    $settings->enabled = true;
    $settings->provider = StorageProvider::AWS->value;
    $settings->key = 'key';
    $settings->secret = 'super-secret';
    $settings->bucket = 'bucket';
    $settings->region = 'us-east-1';
    $settings->endpoint = 'https://s3.us-east-1.amazonaws.com';
    $settings->url = 'https://cdn.example.com';
    $settings->save();

    $this->actingAs($this->user)
        ->put(route('system-settings.update-storage-settings', ['slug' => $this->workspaceSlug()]), [
            'enabled' => false,
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $settings->refresh();
    expect($settings->enabled)->toBeFalse();
    // 其它字段不应被覆盖
    expect($settings->secret)->toBe('super-secret');
    expect($settings->provider)->toBe(StorageProvider::AWS->value);
});

test('enabling storage requires secret when no secret has been saved yet', function () {
    /** @var StorageSettings $settings */
    $settings = app(StorageSettings::class);
    $settings->enabled = false;
    $settings->secret = null;
    $settings->save();

    $this->actingAs($this->user)
        ->put(route('system-settings.update-storage-settings', ['slug' => $this->workspaceSlug()]), [
            'enabled' => true,
            'provider' => StorageProvider::AWS->value,
            'region' => 'us-east-1',
            'endpoint' => 'https://s3.us-east-1.amazonaws.com',
            'key' => 'key',
            'secret' => '',
            'bucket' => 'bucket',
            'url' => 'https://cdn.example.com',
        ])
        ->assertSessionHasErrors('secret');
});

test('authenticated user can enable storage and save settings when connection check passes', function () {
    Storage::shouldReceive('disk')
        ->once()
        ->with(\Mockery::on(fn ($name) => is_string($name) && str_starts_with($name, 'storage_check_')))
        ->andReturn(new class
        {
            public function files($directory = '/', $recursive = false)
            {
                return [];
            }
        });

    $this->actingAs($this->user)
        ->put(route('system-settings.update-storage-settings', ['slug' => $this->workspaceSlug()]), [
            'enabled' => true,
            'provider' => StorageProvider::AWS->value,
            'region' => 'us-east-1',
            'endpoint' => 'https://s3.us-east-1.amazonaws.com',
            'key' => 'key',
            'secret' => 'secret',
            'bucket' => 'bucket',
            'url' => 'https://cdn.example.com',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    /** @var StorageSettings $settings */
    $settings = app(StorageSettings::class)->refresh();
    expect($settings->enabled)->toBeTrue();
    expect($settings->provider)->toBe(StorageProvider::AWS->value);
    expect($settings->secret)->toBe('secret');
});

test('updating storage without providing secret keeps existing secret', function () {
    /** @var StorageSettings $settings */
    $settings = app(StorageSettings::class);
    $settings->enabled = true;
    $settings->provider = StorageProvider::AWS->value;
    $settings->key = 'key';
    $settings->secret = 'old-secret';
    $settings->bucket = 'bucket';
    $settings->region = 'us-east-1';
    $settings->endpoint = 'https://s3.us-east-1.amazonaws.com';
    $settings->url = 'https://cdn.example.com';
    $settings->save();

    Storage::shouldReceive('disk')
        ->once()
        ->with(\Mockery::on(fn ($name) => is_string($name) && str_starts_with($name, 'storage_check_')))
        ->andReturn(new class
        {
            public function files($directory = '/', $recursive = false)
            {
                return [];
            }
        });

    $this->actingAs($this->user)
        ->put(route('system-settings.update-storage-settings', ['slug' => $this->workspaceSlug()]), [
            'enabled' => true,
            'provider' => StorageProvider::AWS->value,
            'region' => 'us-east-1',
            'endpoint' => 'https://s3.us-east-1.amazonaws.com',
            'key' => 'key',
            // 提交空字符串：filled('secret') 为 false，表示保持原值
            'secret' => '',
            'bucket' => 'bucket',
            'url' => 'https://cdn2.example.com',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $settings->refresh();
    expect($settings->secret)->toBe('old-secret');
    expect($settings->url)->toBe('https://cdn2.example.com');
});

test('check connection requires secret when neither provided nor saved', function () {
    /** @var StorageSettings $settings */
    $settings = app(StorageSettings::class);
    $settings->secret = null;
    $settings->save();

    $this->actingAs($this->user)
        ->put(route('system-settings.check-storage-settiings', ['slug' => $this->workspaceSlug()]), [
            'provider' => StorageProvider::AWS->value,
            'region' => 'us-east-1',
            'endpoint' => 'https://s3.us-east-1.amazonaws.com',
            'key' => 'key',
            'secret' => '',
            'bucket' => 'bucket',
            'url' => 'https://cdn.example.com',
        ])
        ->assertSessionHasErrors('secret');
});

test('check connection uses saved secret when secret is not provided', function () {
    /** @var StorageSettings $settings */
    $settings = app(StorageSettings::class);
    $settings->secret = 'saved-secret';
    $settings->save();

    Storage::shouldReceive('disk')
        ->once()
        ->with(\Mockery::on(fn ($name) => is_string($name) && str_starts_with($name, 'storage_check_')))
        ->andReturn(new class
        {
            public function files($directory = '/', $recursive = false)
            {
                return [];
            }
        });

    $this->actingAs($this->user)
        ->put(route('system-settings.check-storage-settiings', ['slug' => $this->workspaceSlug()]), [
            'provider' => StorageProvider::AWS->value,
            'region' => 'us-east-1',
            'endpoint' => 'https://s3.us-east-1.amazonaws.com',
            'key' => 'key',
            'secret' => '',
            'bucket' => 'bucket',
            'url' => 'https://cdn.example.com',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();
});

test('update fails with field error when connection check throws', function () {
    Storage::shouldReceive('disk')
        ->once()
        ->with(\Mockery::on(fn ($name) => is_string($name) && str_starts_with($name, 'storage_check_')))
        ->andReturn(new class
        {
            public function files($directory = '/', $recursive = false)
            {
                throw new RuntimeException('connection failed');
            }
        });

    $this->actingAs($this->user)
        ->put(route('system-settings.update-storage-settings', ['slug' => $this->workspaceSlug()]), [
            'enabled' => true,
            'provider' => StorageProvider::AWS->value,
            'region' => 'us-east-1',
            'endpoint' => 'https://s3.us-east-1.amazonaws.com',
            'key' => 'key',
            'secret' => 'secret',
            'bucket' => 'bucket',
            'url' => 'https://cdn.example.com',
        ])
        ->assertSessionHasErrors('secret');
});

