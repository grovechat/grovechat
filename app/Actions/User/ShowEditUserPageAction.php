<?php

namespace App\Actions\User;

use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowEditUserPageAction
{
    use AsAction;

    public function handle()
    {
        // ...
    }
    
    public function asController()
    {
        return Inertia::render('users/Edit');
    }
}
