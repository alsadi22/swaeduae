<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $ok = true; $checks = [];

        // App info
        $checks['app'] = [
            'env' => config('app.env'),
            'debug' => (bool) config('app.debug'),
            'php' => PHP_VERSION,
            'time' => now()->toIso8601String(),
        ];

        // DB
        try {
            DB::select('select 1');
            $checks['db'] = ['ok' => true];
        } catch (\Throwable $e) {
            $ok = false;
            $checks['db'] = ['ok' => false, 'error' => $e->getMessage()];
        }

        // Cache round-trip
        try {
            $key = 'healthz:'.uniqid('', true);
            Cache::put($key, '1', 60);
            $checks['cache'] = ['ok' => Cache::get($key) === '1'];
            Cache::forget($key);
        } catch (\Throwable $e) {
            $ok = false;
            $checks['cache'] = ['ok' => false, 'error' => $e->getMessage()];
        }

        // Final payload
        $status = $ok ? 200 : 500;
        return response()->json(['ok' => $ok, 'checks' => $checks], $status)
            ->header('Cache-Control', 'no-store');
    }
}
