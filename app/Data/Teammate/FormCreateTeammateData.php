<?php

namespace App\Data\Teammate;

use App\Enums\WorkspaceRole;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class FormCreateTeammateData extends Data
{
    public function __construct(
        public string $name,
        public ?string $nickname,
        public ?string $avatar,
        public WorkspaceRole $role,
        public string $email,
        public string $password,
    ) {}

    public static function rules(): array
    {
        $roles = array_map(static fn (WorkspaceRole $r) => $r->value, WorkspaceRole::assignableCases());

        return [
            'name' => ['required', 'string', 'max:50'],
            'nickname' => ['nullable', 'string', 'max:50'],
            'avatar' => ['nullable', 'string', 'max:2048'],
            'role' => ['required', Rule::in($roles)],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
