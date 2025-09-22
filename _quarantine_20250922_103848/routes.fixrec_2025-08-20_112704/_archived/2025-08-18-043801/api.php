<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
});
