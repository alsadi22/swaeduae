<?php
use Illuminate\Support\Facades\Route;

Route::get('/certificates/verify', function () {
    return redirect('/qr/verify', 302);
});
Route::get('/verify', function () {
    return redirect('/qr/verify', 301);
});
