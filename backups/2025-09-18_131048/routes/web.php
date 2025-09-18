<?php
use Illuminate\Support\Facades\Route;

// Redirect /dashboard to home
Route::redirect('/dashboard', '/')->name('dashboard');

// Public auth routes (no nesting errors)
Route::middleware(['web','guest'])->group(function () {
    // volunteer login & register
    Route::get('/login', fn() => view('auth.login'))->name('login');
    Route::get('/register', fn() => view('auth.register'))->name('register');

    // organization login & register
    Route::get('/org/login', fn() => view('org.auth.login'))->name('org.login');
    Route::get('/org/register', fn() => view('org.auth.register'))->name('org.register');
});
