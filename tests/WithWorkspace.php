<?php

namespace Tests;

use App\Enums\WorkspaceRole;
use App\Models\User;
use App\Models\Workspace;

trait WithWorkspace
{
    protected ?Workspace $workspace = null;

    /**
     * Create a user with a workspace
     */
    protected function createUserWithWorkspace(array $userAttributes = [], array $workspaceAttributes = []): User
    {
        $user = User::factory()->create($userAttributes);
        $this->workspace = Workspace::factory()->create($workspaceAttributes);
        $user->workspaces()->attach($this->workspace, ['role' => WorkspaceRole::OWNER->value]);

        return $user;
    }

    /**
     * Attach workspace to an existing user
     */
    protected function attachWorkspace(User $user, ?Workspace $workspace = null, string $role = 'owner'): Workspace
    {
        $this->workspace = $workspace ?? Workspace::factory()->create();
        $user->workspaces()->attach($this->workspace, ['role' => $role]);

        return $this->workspace;
    }

    /**
     * Get the workspace slug for route generation
     */
    protected function workspaceSlug(): string
    {
        return $this->workspace?->slug ?? 'default';
    }
}
