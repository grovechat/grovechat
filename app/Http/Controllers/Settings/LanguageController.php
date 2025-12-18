<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class LanguageController extends Controller
{
    /**
     * Display the language and timezone settings page.
     */
    public function edit(): Response
    {
        return Inertia::render('settings/Language');
    }
}
