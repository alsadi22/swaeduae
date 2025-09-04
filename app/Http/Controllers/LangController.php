<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LangController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        if (! in_array($locale, ['ar','en'], true)) {
            $locale = 'ar';
        }
        return redirect()->back(302)->withCookie(cookie('app_locale', $locale, 60*24*365));
    }
}
