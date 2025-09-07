<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceHeartbeatController;

// --- Swaed: Volunteer heartbeat (Sanctum, pure API) ---
Route::middleware(['auth:sanctum','throttle:api'])
    ->withoutMiddleware([
        \App\Http\Middleware\EnforceOrgRegistration::class,
        \App\Http\Middleware\MicroCache::class,
        \App\Http\Middleware\SetLocaleAndHeaders::class,
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        \Illuminate\Session\Middleware\StartSession::class,
    ])
    ->post('v1/attendance/heartbeat', [AttendanceHeartbeatController::class, 'store'])
    ->name('api.attendance.heartbeat');

// (keep the rest of your api routes below)
