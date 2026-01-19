<?php

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\WithWorkspace;

uses(RefreshDatabase::class, WithWorkspace::class);

beforeEach(function () {
    $this->user = $this->createUserWithWorkspace();
});

test('authenticated user can view tag list page', function () {
    Tag::factory()->create([
        'workspace_id' => $this->workspace->id,
        'name' => 'VIP',
        'color' => '#ff0000',
        'description' => '重要客户',
    ]);

    $this->actingAs($this->user)
        ->get(route('workspace-setting.datas.tag', ['slug' => $this->workspaceSlug()]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('tags/Index')
            ->has('tag_list', 1)
            ->where('tag_list.0.name', 'VIP')
            ->etc()
        );
});

test('can create a tag in current workspace', function () {
    $this->actingAs($this->user)
        ->post(route('create-tag', ['slug' => $this->workspaceSlug()]), [
            'name' => '新标签',
            'color' => '#00ff00',
            'description' => '用于测试',
        ])
        ->assertRedirect(route('workspace-setting.datas.tag', ['slug' => $this->workspaceSlug()]));

    expect(Tag::query()->where('workspace_id', $this->workspace->id)->where('name', '新标签')->exists())
        ->toBeTrue();
});

test('cannot create duplicate tag name in the same workspace', function () {
    Tag::factory()->create([
        'workspace_id' => $this->workspace->id,
        'name' => '重复标签',
    ]);

    $this->actingAs($this->user)
        ->post(route('create-tag', ['slug' => $this->workspaceSlug()]), [
            'name' => '重复标签',
        ])
        ->assertSessionHasErrors(['name']);
});

test('can update a tag in current workspace', function () {
    $tag = Tag::factory()->create([
        'workspace_id' => $this->workspace->id,
        'name' => '旧名称',
        'color' => '#111111',
    ]);

    $this->actingAs($this->user)
        ->put(route('update-tag', ['slug' => $this->workspaceSlug(), 'id' => $tag->id]), [
            'name' => '新名称',
            'color' => '#222222',
            'description' => '更新描述',
        ])
        ->assertRedirect(route('workspace-setting.datas.tag', ['slug' => $this->workspaceSlug()]));

    $tag->refresh();
    expect($tag->name)->toBe('新名称');
    expect($tag->color)->toBe('#222222');
    expect($tag->description)->toBe('更新描述');
});

test('can delete a tag in current workspace', function () {
    $tag = Tag::factory()->create([
        'workspace_id' => $this->workspace->id,
    ]);

    $this->actingAs($this->user)
        ->delete(route('delete-tag', ['slug' => $this->workspaceSlug(), 'id' => $tag->id]))
        ->assertRedirect(route('workspace-setting.datas.tag', ['slug' => $this->workspaceSlug()]));

    $this->assertSoftDeleted('tags', [
        'id' => $tag->id,
    ]);
});

test('cannot update tag outside current workspace', function () {
    $other = Tag::factory()->create();

    $this->actingAs($this->user)
        ->put(route('update-tag', ['slug' => $this->workspaceSlug(), 'id' => $other->id]), [
            'name' => '非法更新',
        ])
        ->assertNotFound();
});
