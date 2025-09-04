<?php

use Illuminate\Support\Facades\Route;

// Named alias for POST /register → Auth\RegisterController@register
if (! Route::has('register.perform')) {
    Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register'])
        ->name('register.perform');
}
