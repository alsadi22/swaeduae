<?php

use Illuminate\Support\Facades\Route;

Route::get('/organizations', function () {
    if (view()->exists('pages.organizations')) return view('pages.organizations');
    return view('welcome')->with('message', __('Organizations page coming soon.'));
})->name('public.organizations');
