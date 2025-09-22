<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
| Canonical auth endpoints
*/
Route::middleware(['web','guest'])->group(function () {
    // Serve the login form directly (no redirect loops)
    // Existing login handler
    Route::post('/login', [AuthController::class,'login'])->name('login.post'->middleware(['honeypot','throttle:login']));

    // Keep legacy org login URL but redirect one-way to /login
    // Disable self-serve registration for now (avoid 500)
    Route::get('/register', function () { return redirect('/login', 302); })->name('register.disabled');
    Route::post('/register', function () { abort(404->middleware(['honeypot']))->name('register.perform'); });
});
