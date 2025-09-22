<?php

use Illuminate\Support\Facades\Route;

/**
 * PUBLIC OPPORTUNITIES
 */
Route::get('/opportunities', [\App\Http\Controllers\OpportunityBrowseController::class, 'index'])
    ->name('opportunities.index');

Route::get('/opportunities/{opportunity}', [\App\Http\Controllers\OpportunityController::class, 'show'])
    ->name('opportunities.show');

/**
 * APPLY / WITHDRAW (auth + verified)
 */
Route::middleware(['auth','verified'])->group(function () {
    Route::post('/opportunities/{opportunity}/apply', [\App\Http\Controllers\ApplicationController::class, 'apply'])
        ->name('opportunities.apply');

    Route::delete('/opportunities/{opportunity}/apply', [\App\Http\Controllers\ApplicationController::class, 'withdraw'])
        ->name('opportunities.withdraw');

    // Attendance tools
    Route::get('/opportunities/{opportunity}/qr', [\App\Http\Controllers\AttendanceController::class, 'qr'])
        ->name('opportunities.qr');
    Route::get('/attendance/check-in', [\App\Http\Controllers\AttendanceController::class, 'checkIn'])
        ->name('attendance.checkin');
    Route::get('/attendance/check-out', [\App\Http\Controllers\AttendanceController::class, 'checkOut'])
        ->name('attendance.checkout');
    Route::get('/opportunities/{opportunity}/attendances.csv', [\App\Http\Controllers\AttendanceController::class, 'exportCsv'])
        ->name('opportunities.attendances.csv');
    Route::get('/opportunities/{opportunity}/kiosk', [\App\Http\Controllers\AttendanceController::class, 'kiosk'])
        ->name('opportunities.kiosk');
});

/**
 * CERTIFICATES
 */
Route::post('/opportunities/{opportunity}/certificates/issue', [\App\Http\Controllers\CertificateController::class, 'issueForOpportunity'])
    ->middleware('auth')->name('certificates.issue');
Route::get('/verify/{code}', [\App\Http\Controllers\CertificateController::class, 'verify'])
    ->name('certificates.verify');
Route::get('/certificates/{code}', [\App\Http\Controllers\CertificateController::class, 'show'])
    ->name('certificates.show');

/**
 * ORG SELF-SERVICE (auth + verified)
 */
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/org/opportunities', [\App\Http\Controllers\OrgOpportunityController::class, 'index'])
        ->name('org.opps.index');
    Route::get('/org/opportunities/create', [\App\Http\Controllers\OrgOpportunityController::class, 'create'])
        ->name('org.opps.create');
    Route::post('/org/opportunities', [\App\Http\Controllers\OrgOpportunityController::class, 'store'])
        ->name('org.opps.store');
    Route::get('/org/opportunities/{opportunity}/edit', [\App\Http\Controllers\OrgOpportunityController::class, 'edit'])
        ->name('org.opps.edit');
    Route::put('/org/opportunities/{opportunity}', [\App\Http\Controllers\OrgOpportunityController::class, 'update'])
        ->name('org.opps.update');
    Route::delete('/org/opportunities/{opportunity}', [\App\Http\Controllers\OrgOpportunityController::class, 'destroy'])
        ->name('org.opps.destroy');
});

/**
 * LEGACY REDIRECTS -> new org routes
 */
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/organization/opportunities', fn () => redirect()->route('org.opps.index'))
        ->name('org.legacy.index');
    Route::get('/organization/opportunities/create', fn () => redirect()->route('org.opps.create'))
        ->name('org.legacy.create');
    Route::get(
        '/organization/opportunities/{opportunity}/edit',
        fn (\App\Models\Opportunity $opportunity) => redirect()->route('org.opps.edit', $opportunity)
    )->name('org.legacy.edit');
});
