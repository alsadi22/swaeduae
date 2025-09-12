<?php

use Illuminate\Support\Facades\Route;

// Protect common org dashboards if not already guarded
Route::middleware(['web','auth:org'])->group(function () {
    Route::get('/org/dashboard', fn() => app()->handle(request()))->name('org.dashboard.guard.wrap');
    // Add more exact pages if necessary (attendance, reports, etc.)
});
