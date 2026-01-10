<?php

namespace App\Actions\Dashboard;

use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowDashboardAction
{
    use AsAction;

    public function handle()
    {
        // ...
    }
    
    public function asController()
    {
        return Inertia::render('Dashboard');
    }
}
