<?php
use Illuminate\Support\Facades\Route;

Route::get('/admin/login', function () {
    // Reuse the standard login (works with Breeze/UI/Fortify, etc.)
    return redirect()->to('/login?admin=1');
})->name('admin.login');
