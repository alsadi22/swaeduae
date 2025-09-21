<?php
use Illuminate\Support\Facades\Route;

/**
 * View-only public overrides. Loaded last.
 * Keep minimal; avoid duplicating core named routes.
 */

/* Organizations */
Route::view('/organizations', 'public.organizations')->name('organizations');

/* Opportunities */
Route::view('/opportunities', 'public.opportunities')->name('opportunities.index');
Route::get('/opportunities/{idOrSlug}', function (string $idOrSlug) {
    return view('public.opportunity', ['slug' => $idOrSlug]);
})->where('idOrSlug', '[A-Za-z0-9\-_]+')->name('opportunities.show');

/* Static fallbacks */
Route::view('/privacy', 'public.privacy')->name('privacy');
Route::view('/terms',   'public.terms')->name('terms');
Route::view('/faq',     'public.faq')->name('faq');
