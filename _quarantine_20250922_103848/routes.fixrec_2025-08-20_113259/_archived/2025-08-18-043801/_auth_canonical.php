<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AdminLoginController;

// --- Admin auth (guest pages + protected logout) ---
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::get('/login',  [AdminLoginController::class, 'show'])
        ->name('admin.login')->middleware('guest');

    Route::post('/login', [AdminLoginController::class, 'login'])
        ->name('admin.login.post')->middleware('guest'->middleware(['honeypot','throttle:login']));

    Route::post('/logout', [AdminLoginController::class, 'logout'])
        ->name('admin.logout')->middleware('auth');
});

// --- Organization aliases to the main login/register (must be guest) ---
Route::middleware(['web','guest'])->group(function () {
    Route::get('/org/login',    fn() => redirect('/login?type=organization'))->name('org.login')->middleware('guest');
    Route::get('/org/register', fn() => redirect('/register?type=organization'))->name('org.register')->middleware('guest');
});
