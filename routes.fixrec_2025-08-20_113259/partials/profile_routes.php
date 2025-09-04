<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Volunteer\ProfileController;

Route::middleware(['web','auth'])->group(function () {
    // canonical profile page + name used in views
    Route::get('/volunteer/profile', [ProfileController::class, 'index'])->name('volunteer.profile');

    // optional alias so /profile also works
    Route::get('/profile', fn () => redirect('/volunteer/profile', 301));

    // existing update handler
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'update'])->name('profile.password.update');

    // old dashboards should land on the profile
    Route::redirect('/volunteer/dashboard', '/volunteer/profile', 301)->name('volunteer.dashboard.alt');
});

/*
| Profile password update (appended)
*/
Route::post('/profile/password', [\App\Http\Controllers\Volunteer\ProfilePasswordController::class, 'update'])
    ->name('profile.password.update');

// --- ORG PING TEST (TEMP, early-loaded partial) ---
Route::get('/org/ping', function () {
    return 'pong';
})->name('org.ping');

// ========== Organization Area (Org Dashboard MVP) ==========
Route::middleware(['web','auth', \'org:org'])->prefix('org')->as('org.')->group(function () {
    Route::get('/events', [\App\Http\Controllers\Org\EventController::class, 'index'])->name('events.index');
    Route::get('/events/{opportunity}/volunteers', [\App\Http\Controllers\Org\EventController::class, 'volunteers'])->name('events.volunteers');
    Route::get('/events/{opportunity}/export', [\App\Http\Controllers\Org\EventController::class, 'export'])->name('events.export');
    Route::post('/attendances/{attendance}/minutes', [\App\Http\Controllers\Org\EventController::class, 'updateMinutes'])->name('attendances.minutes.update');
});
