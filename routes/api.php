<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('v1/health', function () {
    return response()->json([
        'ok'  => true,
        'ts'  => \Illuminate\Support\Carbon::now()->toIso8601String(),
        'app' => config('app.name'),
        'env' => app()->environment(),
    ], 200);
})->name('api.v1.health');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->post(
    'v1/attendance/heartbeat',
    [\App\Http\Controllers\Api\AttendanceHeartbeatController::class, 'store']
)->name('attendance.heartbeat');
