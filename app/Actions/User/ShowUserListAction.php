<?php

namespace App\Actions\User;

use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowUserListAction
{
    use AsAction;

    public function handle()
    {
        // ...
    }
    
    public function asController()
    {
        return Inertia::render('user/List');
    }
}
