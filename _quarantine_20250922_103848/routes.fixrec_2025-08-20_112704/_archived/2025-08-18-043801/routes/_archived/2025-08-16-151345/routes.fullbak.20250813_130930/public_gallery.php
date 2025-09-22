<?php

use Illuminate\Support\Facades\Route;

Route::get('/gallery', function () {
    if (view()->exists('pages.gallery')) return view('pages.gallery');
    return view('welcome')->with('message', __('Gallery page coming soon.'));
})->name('public.gallery');
