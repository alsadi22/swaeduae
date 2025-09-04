<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileHoursController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $rows = DB::table('volunteer_hours as vh')
            ->join('events as e', 'e.id', '=', 'vh.event_id')
            ->where('vh.user_id', $user->id)
            ->orderByDesc('e.date')
            ->select([
                'e.title as event_title',
                'e.date as event_date',
                'vh.hours',
                'vh.check_in_at',
                'vh.check_out_at',
                'vh.notes',
            ])
            ->paginate(20);

        $total = DB::table('volunteer_hours')
            ->where('user_id', $user->id)
            ->sum('hours');

        return view('profile.hours', compact('rows', 'total'));
    }
}
