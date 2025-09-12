<?php
use Illuminate\Support\Facades\Route;
Route::middleware(['web','guest'])->match(['GET','HEAD'], '/forgot-password', function () {
    return view('auth.forgot-password'); // contains @csrf and posts to password.email
})->name('password.request');
