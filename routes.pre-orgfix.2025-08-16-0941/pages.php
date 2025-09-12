<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

/*
| Public content pages (kept minimal and theme-consistent)
*/
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/partners', [PageController::class, 'partners'])->name('partners');

Route::get('/contact', [PageController::class, 'contactForm'])->name('contact.show');
Route::post('/contact', [PageController::class, 'contactSubmit'])->name('contact.submit');
