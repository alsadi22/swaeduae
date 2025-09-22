<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserAdminController;

/*
 * These are *aliases* so the Argon sidebar can generate URLs.
 * They either redirect to existing pages or to real admin endpoints you already have.
 */
Route::prefix('admin')->middleware(['web','auth','can:isAdmin'])->group(function () {

    // Users (alias name used by theme)
    Route::get('/users/index', function () {
        // you already have route('admin.users') for /admin/users
        return redirect()->route('admin.users');
    })->name('admin.users.index');

    // Opportunities (alias -> public opportunities for now)
    Route::get('/opportunities', fn() => redirect('/opportunities'))
        ->name('admin.opportunities.index');

    // Events (alias -> public events for now)
    Route::get('/events', fn() => redirect('/events'))
        ->name('admin.events.index');
});
