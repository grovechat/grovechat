<?php

namespace App\Actions\User;

use App\Data\UserCreateFormData;
use App\Data\UserCreatePagePropsData;
use App\Models\Workspace;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowCreateUserPageAction
{
    use AsAction;

    public function handle(): UserCreatePagePropsData
    {
        return new UserCreatePagePropsData(
            user_form: new UserCreateFormData,
        );
    }

    public function asController(Workspace $currentWorkspace)
    {
        $props = $this->handle();

        return Inertia::render('user/Create', $props->toArray());
    }
}
