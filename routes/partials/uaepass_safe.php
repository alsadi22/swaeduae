<?php

use Illuminate\Support\Facades\Route;

// If UAEPASS not configured, redirect users to normal login instead of 404/500
Route::get('/auth/uaepass/{any}', fn() => redirect()->route('login'))
    ->where('any','.*');
