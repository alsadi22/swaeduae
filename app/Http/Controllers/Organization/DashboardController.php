<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $orgEvents = Event::where('created_by', Auth::id())->orderBy('date', 'asc')->get();
        $totalVolunteers = $orgEvents->sum(fn($event) => $event->volunteers->count());
        $totalHours = $orgEvents->sum(fn($event) => $event->volunteers->sum('pivot.hours'));

        return view('dashboards.organization', compact('orgEvents', 'totalVolunteers', 'totalHours'));
    }
}
