<?php

namespace App\Actions\Fortify;

use App\Enums\WorkspaceRole;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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
            'name' => ['required', 'string', 'max:255', 'unique:users', 'unique:workspaces,path', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => $this->passwordRules(),
        ])->validate();

        return DB::transaction(function () use ($input) {
            // 创建用户
            $user = User::query()->create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
            ]);

            // 创建工作区
            $workspace = Workspace::query()->create([
                'name' => 'Default',
                'owner_id' => $user->id,
            ]);
            $workspace->slug = $workspace->id;
            $workspace->save();

            $user->workspaces()->attach($workspace->id, ['role' => WorkspaceRole::ADMIN]);

            return $user;
        });
    }
}
