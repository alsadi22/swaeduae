<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

// Org/Admin generate QR for an opportunity
Route::middleware(['auth','role:org|admin'])->group(function () {
    Route::get('/org/attendance/{opportunity}/qr', [AttendanceController::class, 'qr'])->name('attendance.qr');
});

// Public token endpoints (no auth)
Route::get('/a/{token}/in',  [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
Route::get('/a/{token}/out', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');
