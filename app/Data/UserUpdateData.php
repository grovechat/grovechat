<?php

namespace App\Data;

use App\Enums\WorkspaceRole;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class UserUpdateData extends Data
{
    public function __construct(
        public string $name,
        public ?string $nickname,
        public ?string $avatar,
        public WorkspaceRole $role,
        public string $email,
        public ?string $password = null,
    ) {}

    public static function rules(): array
    {
        $roles = array_map(static fn (WorkspaceRole $r) => $r->value, WorkspaceRole::cases());
        $userId = request()->route('id');

        return [
            'name' => ['required', 'string', 'max:50'],
            'nickname' => ['nullable', 'string', 'max:50'],
            'avatar' => ['nullable', 'string', 'max:2048'],
            'role' => ['required', Rule::in($roles)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}
