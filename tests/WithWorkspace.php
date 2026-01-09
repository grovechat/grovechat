<?php

namespace Tests;

use App\Models\Workspace;
use App\Models\User;

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
        $user->workspaces()->attach($this->workspace, ['role' => 'owner']);

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
     * Get the workspace path for route generation
     */
    protected function workspacePath(): string
    {
        return $this->workspace?->path ?? 'default';
    }
}
