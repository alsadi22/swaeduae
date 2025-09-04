<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Org\SetupController;
use App\Http\Controllers\Org\DashboardController;
use App\Http\Controllers\Org\OpportunityController;
use App\Http\Controllers\Org\ApplicantsController;
use App\Http\Controllers\Org\SettingsController;

Route::middleware(['auth','org:org'])->prefix('org')->as('org.')->group(function () {
    Route::get('/setup', [SetupController::class, 'form'])->name('setup.form');
    Route::post('/setup', [SetupController::class, 'store'])->name('setup.store');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/opportunities/create', [OpportunityController::class, 'create'])->name('opportunities.create');
    Route::post('/opportunities', [OpportunityController::class, 'store'])->name('opportunities.store');
    Route::get('/opportunities/{event}/edit', [OpportunityController::class, 'edit'])->name('opportunities.edit');
    Route::post('/opportunities/{event}', [OpportunityController::class, 'update'])->name('opportunities.update');
    Route::post('/opportunities/{event}/close', [OpportunityController::class, 'close'])->name('opportunities.close');

    Route::get('/opportunities/{event}/applicants', [ApplicantsController::class, 'index'])->name('applicants.index');
    Route::post('/opportunities/{event}/applicants/{app}/decision', [ApplicantsController::class, 'decision'])->name('applicants.decision');
    Route::get('/opportunities/{event}/applicants.csv', [ApplicantsController::class, 'exportCsv'])->name('applicants.export');

    Route::get('/settings', [SettingsController::class,'edit'])->name('settings.edit');
    Route::post('/settings', [SettingsController::class,'update'])->name('settings.update');
});
