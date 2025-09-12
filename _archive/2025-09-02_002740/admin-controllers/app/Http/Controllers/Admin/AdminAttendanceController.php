<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAttendanceController extends Controller
{
    public function index(Event $event)
    {
        // Volunteers who applied for this event
        $vols = DB::table('event_volunteer as ev')
            ->join('users as u', 'u.id', '=', 'ev.user_id')
            ->leftJoin('volunteer_hours as vh', function ($j) use ($event) {
                $j->on('vh.user_id', '=', 'ev.user_id')->where('vh.event_id', '=', $event->id);
            })
            ->where('ev.event_id', $event->id)
            ->select([
                'u.id as user_id',
                'u.name as user_name',
                'u.email as user_email',
                'ev.status as app_status',
                'ev.applied_at',
                'vh.hours',
                'vh.check_in_at',
                'vh.check_out_at',
                'vh.notes',
            ])
            ->orderBy('u.name')
            ->paginate(30);

        return view('admin.events.attendance', compact('event', 'vols'));
    }

    public function update(Request $request, Event $event, $userId)
    {
        $data = $request->validate([
            'hours' => 'nullable|numeric|min:0|max:999',
            'check_in_at' => 'nullable|date',
            'check_out_at' => 'nullable|date',
            'notes' => 'nullable|string|max:255',
        ]);

        $existing = DB::table('volunteer_hours')->where([
            'user_id' => $userId,
            'event_id' => $event->id,
        ])->first();

        $payload = [
            'hours' => isset($data['hours']) ? $data['hours'] : 0,
            'check_in_at' => $data['check_in_at'] ?? null,
            'check_out_at' => $data['check_out_at'] ?? null,
            'notes' => $data['notes'] ?? null,
            'updated_at' => now(),
        ];

        if ($existing) {
            DB::table('volunteer_hours')
                ->where(['user_id' => $userId, 'event_id' => $event->id])
                ->update($payload);
        } else {
            $payload['user_id'] = $userId;
            $payload['event_id'] = $event->id;
            $payload['created_at'] = now();
            DB::table('volunteer_hours')->insert($payload);
        }

        return back()->with('status', __('swaed.hours_updated'));
    }
}
