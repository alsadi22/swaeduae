<?php
// Minimal safe overrides. No 'use' statements.
Route::get('/login', function () { return redirect()->away('https://admin.swaeduae.ae/login'); })->name('login');
