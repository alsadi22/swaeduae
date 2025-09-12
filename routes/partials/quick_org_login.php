<?php
use Illuminate\Support\Facades\Route;
Route::middleware('web')->group(function () {
    Route::get('/org/login', fn() => view('org.login'))->name('org.login');
});
