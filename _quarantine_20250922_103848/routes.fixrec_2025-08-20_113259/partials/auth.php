<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegistrationController;

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegistrationController::class, 'show'])->name('register');
    Route::post('/register', [RegistrationController::class, 'store'])->name('register.store')->name('register.perform');
    // Optional: alias
    Route::get('/signup', fn() => redirect('/register'));
});
