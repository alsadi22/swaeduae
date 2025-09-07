<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LocationPing;
use Illuminate\Http\Request;

class AttendanceHeartbeatController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
            'shift_id' => 'nullable|integer',
        ]);

        $ping = LocationPing::create([
            'user_id' => $request->user()->id,
            'shift_id' => $data['shift_id'] ?? null,
            'lat' => $data['lat'],
            'lng' => $data['lng'],
            'accuracy' => $data['accuracy'] ?? null,
            'captured_at' => now(),
        ]);

        return response()->json(['ok' => true, 'id' => $ping->id]);
    }
}
