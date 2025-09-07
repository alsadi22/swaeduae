<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\LocationPing;

class AttendanceHeartbeatController extends Controller
{
    public function __invoke(Request $request)
    {
        return $this->handle($request);
    }

    // Keep compatibility with existing route definition
    public function store(Request $request)
    {
        return $this->handle($request);
    }

    private function handle(Request $request)
    {
        $data = $request->validate([
            'lat'      => ['required','numeric'],
            'lng'      => ['required','numeric'],
            'accuracy' => ['nullable','numeric'],
        ]);

        try {
            LocationPing::create([
                'user_id'  => (int) $request->user()->id,
                'lat'      => $data['lat'],
                'lng'      => $data['lng'],
                'accuracy' => $data['accuracy'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::error('Heartbeat insert failed', ['error' => $e->getMessage()]);
        }

        // Always return a clean 204
        return response()->noContent();
    }
}
