<?php

use Illuminate\Support\Facades\Route;

Route::get('/partners', function () {
    if (view()->exists('pages.partners')) return view('pages.partners');
    return view('welcome')->with('message', __('Partners page coming soon.'));
})->name('pages.partners');
