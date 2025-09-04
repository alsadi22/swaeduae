<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Volunteer\ProfileController;

Route::middleware(['auth','verified'])->group(function () {
    // Canonical user profile editor
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Legacy alias -> canonical
    Route::get('/volunteer/profile', fn() => redirect('/profile', 301))->name('volunteer.profile');
});
