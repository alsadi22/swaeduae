<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (view()->exists('welcome')) {
        return view('welcome');
    }
    return redirect('/opportunities');
})->name('home');
