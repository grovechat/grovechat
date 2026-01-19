<?php

namespace App\Data\Workspace;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class FormUpdateSystemWorkspaceData extends Data
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $logo_id,
        public string $owner_id,
    ) {}

    public static function rules(): array
    {
        /** @var string|int|null $workspaceId */
        $workspaceId = request()->route('id');

        return [
            'name' => ['required', 'string', 'max:30'],
            'slug' => ['required', 'string', 'max:50', Rule::unique('workspaces', 'slug')->ignore($workspaceId)],
            'logo_id' => ['nullable', 'string'],
            'owner_id' => ['required', 'string', Rule::exists('users', 'id')],
        ];
    }
}
