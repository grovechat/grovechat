<?php

namespace App\Data\Teammate;

use App\Enums\WorkspaceRole;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class FormUpdateTeammateData extends Data
{
    public function __construct(
        public WorkspaceRole $role,
    ) {}

    public static function rules(): array
    {
        $roles = array_map(static fn (WorkspaceRole $r) => $r->value, WorkspaceRole::assignableCases());

        return [
            'role' => ['required', Rule::in($roles)],
        ];
    }
}
