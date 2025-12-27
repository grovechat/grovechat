<?php

namespace Tests;

use App\Models\Tenant;
use App\Models\User;

trait WithTenant
{
    protected ?Tenant $tenant = null;

    /**
     * Create a user with a tenant
     */
    protected function createUserWithTenant(array $userAttributes = [], array $tenantAttributes = []): User
    {
        $user = User::factory()->create($userAttributes);
        $this->tenant = Tenant::factory()->create($tenantAttributes);
        $user->tenants()->attach($this->tenant, ['role' => 'owner']);
        
        return $user;
    }

    /**
     * Attach tenant to an existing user
     */
    protected function attachTenant(User $user, ?Tenant $tenant = null, string $role = 'owner'): Tenant
    {
        $this->tenant = $tenant ?? Tenant::factory()->create();
        $user->tenants()->attach($this->tenant, ['role' => $role]);
        
        return $this->tenant;
    }

    /**
     * Get the tenant path for route generation
     */
    protected function tenantPath(): string
    {
        return $this->tenant?->path ?? 'default';
    }
}
