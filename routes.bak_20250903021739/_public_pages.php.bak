<?php
use Illuminate\Support\Facades\Route;

if (view()->exists('public.faq')) {
    Route::get('/faq', fn() => view('public.faq'))->name('faq');
}
if (view()->exists('public.contact')) {
    Route::get('/contact', fn() => view('public.contact'))->name('contact.show');
}
if (view()->exists('public.terms')) {
    Route::get('/terms', fn() => view('public.terms'))->name('terms');
}
if (view()->exists('public.privacy')) {
    Route::get('/privacy', fn() => view('public.privacy'))->name('privacy');
}
if (view()->exists('public.partners')) {
    Route::get('/partners', fn() => view('public.partners'))->name('partners');
}
