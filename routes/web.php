<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\QR\VerifyController;

// Health

// Public core pages
Route::view('/', 'public.home')->name('home.public');
Route::view('/about', 'public.about')->name('about');
Route::get('/contact', [ContactController::class, 'show'])->name('contact.get');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.submit');
Route::view('/privacy', 'public.privacy')->name('privacy');
Route::view('/terms', 'public.terms')->name('terms');

// QR verify (controller already exists per your earlier route:list)
Route::get('/qr/verify', [VerifyController::class, 'show']);
Route::get('/qr/verify/{serial?}', [VerifyController::class, 'show'])->name('qr.verify');
Route::get('/healthz', \App\Http\Controllers\HealthzController::class)->name('healthz');

