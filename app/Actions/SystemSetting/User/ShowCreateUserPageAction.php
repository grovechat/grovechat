<?php

namespace App\Actions\SystemSetting\User;

use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowCreateUserPageAction
{
    use AsAction;

    public function asController()
    {
        return Inertia::render('admin/user/Create');
    }
}
