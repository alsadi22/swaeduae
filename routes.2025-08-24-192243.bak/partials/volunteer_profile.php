<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Volunteer\ProfileIndexAction;
use App\Http\Controllers\ProfileController;

Route::middleware('auth')->group(function () {
    // Canonical volunteer profile
    Route::get('/profile', ProfileIndexAction::class)->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Legacy URL preserved for old links/bookmarks
    Route::get('/volunteer/profile', function () {
        return redirect()->route('profile');
    })->name('volunteer.profile');
});
