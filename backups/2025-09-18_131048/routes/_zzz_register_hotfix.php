<?php
use Illuminate\Support\Facades\Route;
Route::middleware(['web','guest'])->group(function () {
    Route::get('/register', fn() => view('auth.register'))->name('register');
    Route::get('/org/register', fn() => view('org.auth.register'))->name('org.register');
});
