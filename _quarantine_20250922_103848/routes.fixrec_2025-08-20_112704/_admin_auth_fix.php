<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AdminLoginController;

/*
 * Canonical admin auth routes
 * GET /admin/login (guest)
 * POST /admin/login (guest)
 * POST /admin/logout (auth)
 */
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])
        ->name('admin.login')->middleware('guest');

    Route::post('/login', [AdminLoginController::class, 'login'])
        ->name('admin.login.post')->middleware('guest');

    Route::post('/logout', [AdminLoginController::class, 'logout'])
        ->name('admin.logout')->middleware('auth');
});
