<?php

namespace App\Data\Workspace;

use App\Enums\WorkspaceRole;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class FormAddWorkspaceMemberData extends Data
{
    public function __construct(
        public string $user_id,
        public WorkspaceRole $role,
    ) {}

    public static function rules(): array
    {
        $roles = array_map(static fn (WorkspaceRole $r) => $r->value, WorkspaceRole::assignableCases());

        return [
            'user_id' => ['required', 'string', Rule::exists('users', 'id')],
            'role' => ['required', Rule::in($roles)],
        ];
    }
}
