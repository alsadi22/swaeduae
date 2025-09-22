<?php

use Illuminate\Support\Facades\Route;

Route::get('/categories', function () {
    if (view()->exists('pages.categories')) return view('pages.categories');
    return view('welcome')->with('message', __('Categories page coming soon.'));
})->name('pages.categories');
