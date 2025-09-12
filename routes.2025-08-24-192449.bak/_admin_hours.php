<?php
use Illuminate\Support\Facades\Route;

Route::get('admin/opportunities/{opportunityId}/hours',
    [\App\Http\Controllers\Admin\HoursReportController::class, 'show'])
    ->middleware(['web','auth','can:isAdmin'])
    ->name('admin.hours.show');

# Optional "all" aggregate
Route::get('admin/hours',
    [\App\Http\Controllers\Admin\HoursReportController::class, 'showAll'])
    ->middleware(['web','auth','can:isAdmin'])
    ->name('admin.hours.all');
