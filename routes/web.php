<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('public.home'))->name('home');

Route::view('/about', 'public.home')   // placeholder, swap later
    ->name('pages.about');
require __DIR__.'/z_org_login_override.php';
Route::view('/stories', 'public.stories')->name('pages.stories');
Route::view('/organizations', 'public.organizations.index')->name('pages.organizations');
/** Certificates verify (optional {code}) */
Route::get('/certificates/verify/{code?}', function (?string $code = null) {
    // Renders resources/views/public/certificates/verify.blade.php
    return view('public.certificates.verify', ['code' => $code]);
})->name('certificates.verify.form');

/** Certificates verify canonical */
