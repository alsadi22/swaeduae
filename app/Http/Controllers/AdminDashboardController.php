<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Basic counts
        $totalVolunteers = DB::table('users')->where('role', 'volunteer')->count();
        $totalOrganizations = DB::table('users')->where('role', 'org')->count();
        $totalOpportunities = DB::table('opportunities')->count();
        $totalHours = DB::table('volunteer_hours')->sum('hours');

        // Volunteer leaderboard (top 5)
        $volunteerLeaderboard = DB::table('volunteer_hours')
            ->join('users', 'volunteer_hours.user_id', '=', 'users.id')
            ->select('users.name', DB::raw('SUM(volunteer_hours.hours) as total_hours'))
            ->groupBy('users.name')
            ->orderByDesc('total_hours')
            ->limit(5)
            ->get();

        // Organization leaderboard (top 5 by hours)
        $orgLeaderboard = DB::table('opportunities')
            ->join('users', 'opportunities.user_id', '=', 'users.id')
            ->join('volunteer_hours', 'opportunities.id', '=', 'volunteer_hours.opportunity_id')
            ->where('users.role', 'org')
            ->select('users.name', DB::raw('SUM(volunteer_hours.hours) as total_hours'))
            ->groupBy('users.name')
            ->orderByDesc('total_hours')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalVolunteers', 'totalOrganizations', 'totalOpportunities', 'totalHours',
            'volunteerLeaderboard', 'orgLeaderboard'
        ));
    }
}
