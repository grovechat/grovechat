<?php

namespace App\Data\Teammate;

use App\Enums\WorkspaceRole;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class FormCreateTeammateData extends Data
{
    public function __construct(
        public string $user_id,
        public ?string $nickname,
        public WorkspaceRole $role,
    ) {}

    public static function rules(): array
    {
        return [
            'user_id' => ['required', 'string', Rule::exists('users', 'id')],
            'nickname' => ['nullable', 'string', 'max:50'],
            'role' => ['required', Rule::enum(WorkspaceRole::class)->only(WorkspaceRole::assignableCases())],
        ];
    }
}
