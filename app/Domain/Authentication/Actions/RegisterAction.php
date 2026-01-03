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
     * @param array $data
     * @return array
     */
    public function execute(array $data): array
    {
        $validatedData = RegisterData::from($data);

        $user = DB::transaction(function () use ($validatedData) {
            // 创建用户
            $user = User::create([
                'name' => $validatedData->name,
                'email' => $validatedData->email,
                'password' => $validatedData->password,
            ]);

            // 创建租户
            $tenant = Tenant::create([
                'name' => $user->name, // 以用户名作为默认租户名
                'slug' => Str::lower($validatedData->name) . '-' . Str::lower(Str::random(4)),
                'path' => strtolower($user->name), // 路径强制小写
            ]);

            // 关联用户和租户
            $user->tenants()->attach($tenant->id, ['role' => TenantRole::ADMIN]);

            return $user;
        });

        return [
            'user_id' => $user->id,
            'email' => $user->email,
        ];
    }
}
