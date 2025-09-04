<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::post('login', [LoginController::class, 'login'])
    ->name('login')
    ->middleware([\App\Http\Middleware\Honeypot::class,'throttle:login']);

Route::post('register', [RegisterController::class, 'register'])
    ->name('register')
    ->middleware([\App\Http\Middleware\Honeypot::class]);
