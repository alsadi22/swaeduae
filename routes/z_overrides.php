<?php
use Illuminate\Support\Facades\Route;

/**
 * Public-only overrides (GET) to ensure themed views render.
 * - We do NOT change POST logic or controller behavior.
 * - If views are missing, we fall back or redirect.
 */

// /stories -> themed placeholder view (until real controller/content lands)
Route::get('/stories', function () {
    if (view()->exists('public.stories')) {
        return view('public.stories');
    }
    return abort(404);
});

// /org/login GET -> force themed org login view (POST stays on original route)
Route::get('/org/login', function () {
    // Try common org login view locations we created
    foreach (['org.login','org.auth.login','auth.org_login'] as $v) {
        if (view()->exists($v)) {
            return view($v);
        }
    }
    // Fallback: send to /login rather than show an unthemed page
    return redirect('/login');
});
