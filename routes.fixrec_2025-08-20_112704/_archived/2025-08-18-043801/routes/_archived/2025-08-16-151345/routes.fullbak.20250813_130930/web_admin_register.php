<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\OrganizationController as AdminOrganizationController;
use App\Http\Controllers\Admin\OpportunityController as AdminOpportunityController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\SettingsController;

Route::prefix('admin')
    ->middleware(['web','auth','admin'])  // assumes AdminMiddleware is bound as 'admin'
    ->name('admin.')
    ->group(function () {
        // Core
        Route::get('/', [AdminDashboardController::class, 'index'])->name('home');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Media / Settings (already existed, keep names consistent)
        Route::get('/media', [MediaController::class, 'index'])->name('media');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');

        // Users
        Route::resource('users', UserAdminController::class)->parameters(['users' => 'user']);
        Route::get('users/export/csv', [UserAdminController::class, 'exportCsv'])->name('users.export.csv');

        // Organizations
        Route::resource('organizations', AdminOrganizationController::class)->parameters(['organizations' => 'organization']);
        Route::get('organizations/export/csv', [AdminOrganizationController::class, 'exportCsv'])->name('organizations.export.csv');

        // Opportunities
        Route::resource('opportunities', AdminOpportunityController::class)->parameters(['opportunities' => 'opportunity']);
        Route::get('opportunities/export/csv', [AdminOpportunityController::class, 'exportCsv'])->name('opportunities.export.csv');

        // Events
        Route::resource('events', AdminEventController::class)->parameters(['events' => 'event']);
        Route::get('events/export/csv', [AdminEventController::class, 'exportCsv'])->name('events.export.csv');
    });

Route::get('admin/settings', [\App\Http\Controllers\Admin\SiteSettingsController::class, 'index'])
    ->name('admin.settings');
Route::post('admin/settings', [\App\Http\Controllers\Admin\SiteSettingsController::class, 'update'])
    ->name('admin.settings.update');
