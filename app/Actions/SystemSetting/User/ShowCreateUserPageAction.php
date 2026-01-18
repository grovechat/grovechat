<?php

namespace App\Actions\SystemSetting\User;

use App\Data\User\CreateUserFormData;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowCreateUserPageAction
{
    use AsAction;

    public function handle(): CreateUserFormData
    {
        return new CreateUserFormData;
    }

    public function asController()
    {
        return Inertia::render('admin/user/Create', $this->handle()->toArray());
    }
}
