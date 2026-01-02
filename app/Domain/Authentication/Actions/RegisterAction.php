<?php

namespace App\Domain\Authentication\Actions;

use App\Domain\Authentication\DTOs\RegisterData;
use App\Enums\TenantRole;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegisterAction
{
    /**
     * 执行用户注册操作
     *
     * @param RegisterData $data
     * @return User
     */
    public function execute(RegisterData $data): User
    {
        return DB::transaction(function () use ($data) {
            // 创建用户
            $user = User::create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => $data->password,
            ]);

            // 创建租户
            $tenant = Tenant::create([
                'name' => $user->name, // 以用户名作为默认租户名
                'slug' => Str::lower($data->name) . '-' . Str::lower(Str::random(4)),
                'path' => strtolower($user->name), // 路径强制小写
            ]);

            // 关联用户和租户
            $user->tenants()->attach($tenant->id, ['role' => TenantRole::ADMIN]);

            return $user;
        });
    }
}
