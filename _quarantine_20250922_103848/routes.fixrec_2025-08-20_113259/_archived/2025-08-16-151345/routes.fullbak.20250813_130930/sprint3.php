<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\ContactController;

if (class_exists(RateLimiter::class)) {
    RateLimiter::for('contact', function (Request $request) {
        return Limit::perMinute(5)->by($request->ip());
    });
}

Route::get('/about',    [PageController::class, 'about'])->name('about');
Route::get('/faq',      [PageController::class, 'faq'])->name('faq');
Route::get('/partners', [PageController::class, 'partners'])->name('partners');

Route::get('/regions',        [PageController::class, 'regionsIndex'])->name('regions.index');
Route::get('/regions/{slug}', [PageController::class, 'regionShow'])->name('regions.show');

Route::get('/contact',  [ContactController::class, 'form'])->name('contact.form');
Route::post('/contact', [ContactController::class, 'submit'])
    ->middleware('throttle:contact')
    ->name('contact.submit'->middleware(['honeypot']));

// Back-compat named aliases for older nav links
Route::get('/about-legacy', fn() => redirect()->route('about'))->name('about.page');
Route::get('/faq-legacy', fn() => redirect()->route('faq'))->name('faq.page');
Route::get('/partners-legacy', fn() => redirect()->route('partners'))->name('partners.page');
