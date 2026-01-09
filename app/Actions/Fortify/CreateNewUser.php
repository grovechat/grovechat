<?php

namespace App\Actions\Fortify;

use App\Enums\TenantRole;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        // 验证输入
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255', 'unique:users', 'unique:tenants,path', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => $this->passwordRules(),
        ])->validate();

        return DB::transaction(function () use ($input) {
            // 创建用户
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
            ]);

            // 创建租户
            $tenant = Tenant::create([
                'name' => 'Default',
                'path' => strtolower($user->name),
                'owner_id' => $user->id,
            ]);
            $tenant->createSlug();
            
            $user->tenants()->attach($tenant->id, ['role' => TenantRole::ADMIN]);

            return $user;
        });
    }
}
