<?php

namespace App\Actions\SystemSetting\User;

use App\Data\User\SystemUserCreateFormData;
use App\Data\User\SystemUserCreatePagePropsData;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowCreateSystemUserPageAction
{
    use AsAction;

    public function handle(): SystemUserCreatePagePropsData
    {
        return new SystemUserCreatePagePropsData(
            user_form: new SystemUserCreateFormData,
        );
    }

    public function asController()
    {
        return Inertia::render('admin/user/Create', $this->handle()->toArray());
    }
}
