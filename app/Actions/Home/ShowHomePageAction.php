<?php

namespace App\Actions\Home;

use Inertia\Inertia;
use Laravel\Fortify\Features;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowHomePageAction
{
    use AsAction;

    public function handle()
    {
        // ...
    }
    
    public function asController()
    {
        return Inertia::render('Welcome', [
            'canRegister' => Features::enabled(Features::registration()),
        ]);
    }
}
