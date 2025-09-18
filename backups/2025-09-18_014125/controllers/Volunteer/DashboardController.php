<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $volunteer = Auth::user();
        $events = $volunteer->events()->orderBy('date')->take(5)->get();
        $totalHours = $volunteer->hours()->sum('hours');
        $certificates = $volunteer->certificates;
        return view('volunteer.dashboard', compact('events','totalHours','certificates'));
    }

    public function hours()
    {
        $volunteer = Auth::user();
        $hours = $volunteer->hours()->with('event')->get();
        return view('volunteer.hours', compact('hours'));
    }

    public function leaderboard()
    {
        $leaders = \App\Models\User::role('volunteer')->withSum('hours','hours')->orderBy('hours_sum','desc')->take(10)->get();
        return view('volunteer.leaderboard', compact('leaders'));
    }
}
