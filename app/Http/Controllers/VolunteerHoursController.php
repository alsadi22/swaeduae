<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VolunteerHoursController extends Controller
{
    // GET /volunteer/hours
    public function index()
    {
        $uid    = Auth::id();
        $hours  = DB::table('volunteer_hours')->where('user_id', $uid)->orderByDesc('id')->get();
        $events = DB::table('events')->select('id','title')->orderByDesc('date')->limit(100)->get();

        return view('volunteer.hours', [
            'hours'  => $hours,
            'events' => $events,
        ]);
    }

    // POST /volunteer/hours
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|integer|exists:events,id',
            'hours'    => 'required|numeric|min:0.5|max:24',
        ]);

        $uid = Auth::id();

        DB::table('volunteer_hours')->insert([
            'user_id'    => $uid,
            'event_id'   => (int) $request->event_id,
            'hours'      => (float) $request->hours,
            'approved'   => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('volunteer.hours')->with('success', __('Hours submitted and pending approval.'));
    }
}
