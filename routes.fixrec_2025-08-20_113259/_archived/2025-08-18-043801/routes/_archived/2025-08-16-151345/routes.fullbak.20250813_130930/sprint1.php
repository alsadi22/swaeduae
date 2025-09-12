<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ApplicationReviewController;
use App\Http\Controllers\Admin\AttendanceController;

Route::middleware(['web','auth','admin'])->group(function() {
    Route::get('/admin/applications', [ApplicationReviewController::class, 'index'])->name('admin.applications.index');
    Route::post('/admin/applications/bulk', [ApplicationReviewController::class, 'bulk'])->name('admin.applications.bulk');

    Route::prefix('/admin/opportunities/{id}')->group(function() {
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('admin.opportunities.attendance');
        Route::get('/scan', [AttendanceController::class, 'scan'])->name('admin.opportunities.scan');
        Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('admin.opportunities.checkin');
        Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('admin.opportunities.checkout');
        Route::post('/attendance/finalize', [AttendanceController::class, 'finalize'])->name('admin.opportunities.finalize');
        Route::post('/attendance/adjust', [AttendanceController::class, 'adjust'])->name('admin.opportunities.adjust');
    });
});
