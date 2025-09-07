<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LocationPing;
use Illuminate\Http\Request;

class AttendanceHeartbeatController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'accuracy' => 'nullable|numeric',
        ]);

        LocationPing::create([
            'user_id' => $request->user()->id,
            'lat' => $data['lat'] ?? null,
            'lng' => $data['lng'] ?? null,
            'accuracy' => $data['accuracy'] ?? null,
        ]);

        return response()->noContent();
    }
}
