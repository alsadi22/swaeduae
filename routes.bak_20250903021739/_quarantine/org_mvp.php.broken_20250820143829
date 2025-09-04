<?php

use Illuminate\Support\Facades\Route;

// --- ORG PING TEST (so we can verify quickly) ---
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

// Convenience: /org → /org/dashboard
Route::middleware(['web','auth', \'org:org'])
    ->prefix('org')
    ->group(function () {
        Route::get('/', fn() => redirect()->route('org.dashboard'))->name('org.root');
    });

// --- Canonical /org landing -> DashboardController@index (keep legacy redirect) ---
Route::middleware(['web','auth','role:org'])->prefix('org')->as('org.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Org\DashboardController::class, 'index'])->name('dashboard');
    Route::redirect('/dashboard', '/org', 301)->name('dashboard.legacy');
});
