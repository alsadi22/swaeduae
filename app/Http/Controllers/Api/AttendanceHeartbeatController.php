<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LocationPing;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AttendanceHeartbeatController extends Controller
{
    public function store(Request $request): Response
    {
        $data = $request->validate([
            'lat' => ['required','numeric','between:-90,90'],
            'lng' => ['required','numeric','between:-180,180'],
            'accuracy' => ['nullable','numeric','min:0','max:1000'],
        ]);

        LocationPing::create([
            'user_id' => $request->user()->id,
            'lat' => $data['lat'],
            'lng' => $data['lng'],
            'accuracy' => $data['accuracy'] ?? null,
            'captured_at' => now(),
        ]);

        return response()->noContent();
    }
}
