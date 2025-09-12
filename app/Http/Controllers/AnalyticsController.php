<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use App\Models\Opportunity;
use App\Models\Certificate;

class AnalyticsController extends Controller
{
    public function dashboard()
    {
        $volunteers = User::where('role', 'volunteer')->count();
        $organizations = User::where('role', 'organization')->count();
        $events = Event::count();
        $opportunities = Opportunity::count();
        $certificates = Certificate::count();
        $hours = Certificate::sum('hours');
        return view('analytics.dashboard', compact('volunteers','organizations','events','opportunities','certificates','hours'));
    }
}
