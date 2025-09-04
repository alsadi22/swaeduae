<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\Badge;
use App\Models\VolunteerProfile;
use Carbon\Carbon;

class VolunteerController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();

        // Total hours from attendance
        $totalHours = Attendance::where('volunteer_id',$user->id)->sum('hours');

        $now = Carbon::now();
        $upcomingOpportunities = $user->opportunities()
                                     ->where('start_date','>=',$now)
                                     ->orderBy('start_date')
                                     ->get();
        $pastOpportunities = $user->opportunities()
                                  ->where('start_date','<',$now)
                                  ->orderByDesc('start_date')
                                  ->get();

        $certificates = Certificate::where('volunteer_id',$user->id)->get();

        $badges = [];
        if (class_exists(Badge::class)) {
            $badges = Badge::whereHas('volunteers', function ($q) use ($user) {
                $q->where('users.id',$user->id);
            })->get();
        }

        return view('dashboard.volunteer', compact('user','totalHours','upcomingOpportunities','pastOpportunities','certificates','badges'));
    }

    public function leaderboard()
    {
        $topVolunteers = Attendance::select('volunteer_id')
            ->selectRaw('SUM(hours) as total_hours')
            ->groupBy('volunteer_id')
            ->orderByDesc('total_hours')
            ->take(10)
            ->with('volunteer')
            ->get();

        return view('leaderboard', compact('topVolunteers'));
    }

    public function edit(Request $request)
    {
        $user = $request->user();
        $profile = VolunteerProfile::firstOrNew(['user_id'=>$user->id]);
        return view('profile.edit', compact('user','profile'));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'skills' => 'nullable|string',
            'interests' => 'nullable|string',
            'license_number' => 'nullable|string|max:255',
        ]);

        $profile = VolunteerProfile::firstOrNew(['user_id'=>$user->id]);
        $profile->fill($data);
        $profile->save();

        return back()->with('status','Profile updated successfully.');
    }
}
