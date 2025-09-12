<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // legacy volunteer dashboard -> canonical
    Route::redirect('/volunteer/dashboard', '/dashboard', 301)->name('volunteer.dashboard.alt');
    Route::redirect('/home', '/dashboard', 301);
});
