<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceHeartbeatController;

/*
|--------------------------------------------------------------------------
| API Routes
| These routes are loaded by RouteServiceProvider within the "api" middleware
| group and automatically get the "/api" prefix.
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/* API v1 */
Route::prefix('v1')->group(function () {
    // Health check: GET /api/v1/health
    Route::get('/health', fn () => response()->json([
        'ok' => true,
        'ts' => now()->toIso8601String(),
    ]));

    // Certificate verify: GET /api/v1/certificates/verify/{code}
    Route::get('/certificates/verify/{code}', function (string $code) {
        $c = \App\Models\Certificate::query()->where('code', $code)->first();
        return response()->json([
            'valid' => (bool) $c,
            'certificate' => $c,
        ]);
    });

    // Geofence heartbeat: POST /api/v1/attendance/heartbeat
    Route::middleware(['auth:sanctum','throttle:ping'])
        ->post('/attendance/heartbeat', [AttendanceHeartbeatController::class, 'store'])
        ->name('attendance.heartbeat');
});

// Include agent ping health route if present
if (file_exists(base_path('routes/_agent_ping_api.php'))) { require base_path('routes/_agent_ping_api.php'); }
use Illuminate\Support\Facades\Date;
Route::get('/agent/ping', function (\Illuminate\Http\Request $r) {
    $tok = env('AGENT_TOKEN');
    if ($r->header('X-Agent-Token') !== $tok) {
        return response()->json(['ok'=>false,'reason'=>'forbidden'], 403);
    }
    return response()->json(['ok'=>true,'ts'=>Date::now()->toISOString()], 200);
})->name('agent.ping');

if (file_exists(base_path('routes/_agent_diag_api.php'))) require base_path('routes/_agent_diag_api.php');
