<?php

namespace App\Actions\SystemSetting\User;

use App\Data\SystemUserEditFormData;
use App\Data\SystemUserEditPagePropsData;
use App\Models\User;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowEditSystemUserPageAction
{
    use AsAction;

    public function handle(string $id): SystemUserEditPagePropsData
    {
        $user = User::query()
            ->where('is_super_admin', false)
            ->findOrFail($id);

        return new SystemUserEditPagePropsData(
            user_form: SystemUserEditFormData::fromModel($user),
        );
    }

    public function asController(string $id)
    {
        return Inertia::render('admin/user/Edit', $this->handle($id)->toArray());
    }
}
