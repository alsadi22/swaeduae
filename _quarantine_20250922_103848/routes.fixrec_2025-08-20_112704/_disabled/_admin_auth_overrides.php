<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AdminLoginController;

Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminLoginController::class, 'login'])
        ->name('admin.login.post')
        ->middleware([\App\Http\Middleware\Honeypot::class,'throttle:login']);
});
