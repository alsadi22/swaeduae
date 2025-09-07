<?php

namespace App\Http\Controllers;

use App\Models\LocationPing;
use Illuminate\Http\Request;

class AttendanceHeartbeatController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0|max:2000',
        ]);

        LocationPing::create([
            'user_id' => $request->user()->id,
            'lat' => $data['lat'],
            'lng' => $data['lng'],
            'accuracy' => $data['accuracy'] ?? null,
        ]);

        return response()->noContent();
    }
}
