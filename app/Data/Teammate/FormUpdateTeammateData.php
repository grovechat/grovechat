<?php

namespace App\Data\Teammate;

use App\Enums\WorkspaceRole;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class FormUpdateTeammateData extends Data
{
    public function __construct(
        public ?string $nickname,
        public WorkspaceRole $role,
    ) {}

    public static function rules(): array
    {
        // 更新时允许传入 owner（例如 owner 编辑自己时 role 会作为隐藏字段提交），
        // 具体是否允许变更由 Gate('workspace-users.updateRole') 决定。
        return [
            'nickname' => ['nullable', 'string', 'max:50'],
            'role' => ['required', Rule::enum(WorkspaceRole::class)],
        ];
    }
}
