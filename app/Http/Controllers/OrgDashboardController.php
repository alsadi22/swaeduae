<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class OrgDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role !== 'org') abort(403);

        $totalOpportunities = DB::table('opportunities')->where('user_id', $user->id)->count();
        $totalApplications = DB::table('applications')
            ->join('opportunities', 'applications.opportunity_id', '=', 'opportunities.id')
            ->where('opportunities.user_id', $user->id)->count();
        $totalHours = DB::table('volunteer_hours')
            ->join('opportunities', 'volunteer_hours.opportunity_id', '=', 'opportunities.id')
            ->where('opportunities.user_id', $user->id)->sum('volunteer_hours.hours');

        $upcomingEvents = DB::table('opportunities')
            ->where('user_id', $user->id)->where('date', '>=', now())->orderBy('date')->get();
        $pastEvents = DB::table('opportunities')
            ->where('user_id', $user->id)->where('date', '<', now())->orderByDesc('date')->get();

        return view('org.dashboard', compact('totalOpportunities','totalApplications','totalHours','upcomingEvents','pastEvents'));
    }

    public function exportApplications()
    {
        $user = Auth::user();
        if ($user->role !== 'org') abort(403);

        $applications = DB::table('applications')
            ->join('users', 'applications.user_id', '=', 'users.id')
            ->join('opportunities', 'applications.opportunity_id', '=', 'opportunities.id')
            ->where('opportunities.user_id', $user->id)
            ->select('users.name as Volunteer','opportunities.title as Opportunity','applications.status as Status')
            ->get();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="applications.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Volunteer', 'Opportunity', 'Status']);
        foreach ($applications as $row) {
            fputcsv($out, (array) $row);
        }
        fclose($out);
        exit;
    }
}
