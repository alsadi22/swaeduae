<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AttendanceAdminController;

Route::prefix('admin')->name('admin.')->middleware(['web','auth','can:isAdmin'])->group(function () {
    // List page
    Route::get('/attendance', [AttendanceAdminController::class, 'index'])->name('attendance.index');

    // QR page (tokens for checkin/checkout)
    Route::get('/attendance/qr/{opportunityId}', [AttendanceAdminController::class, 'qr'])->name('attendance.qr');

    // Manual checkin/checkout
    Route::post('/opportunities/{opportunityId}/attendance/manual', [AttendanceAdminController::class, 'manual'])->name('attendance.manual');

    // Finalize hours
    Route::post('/opportunities/{opportunityId}/attendance/finalize', [AttendanceAdminController::class, 'finalize'])->name('attendance.finalize');

    // Mark opportunity complete + issue certificates
    Route::post('/opportunities/{opportunityId}/attendance/complete', [AttendanceAdminController::class, 'complete'])->name('attendance.complete');
});
