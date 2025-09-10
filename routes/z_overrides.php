<?php
// Minimal safe overrides. No 'use' statements.
Route::post('/contact/send', [\App\Http\Controllers\ContactController::class, 'submit'])->name('contact.send');
Route::get('/login', function () { return redirect()->away('https://admin.swaeduae.ae/login'); })->name('login');
