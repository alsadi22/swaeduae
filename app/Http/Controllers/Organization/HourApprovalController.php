<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VolunteerHour;

class HourApprovalController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $pending = VolunteerHour::whereHas('event', function($q) use ($user) {
            $q->where('organization_id', $user->id);
        })->where('approved', 0)->get();
        return view('organization.hours.index', compact('pending'));
    }

    public function approve($id)
    {
        $vh = VolunteerHour::findOrFail($id);
        $vh->approved = 1;
        $vh->save();
        return back()->with('success', 'Hour approved.');
    }
    public function reject($id)
    {
        $vh = VolunteerHour::findOrFail($id);
        $vh->approved = -1;
        $vh->save();
        return back()->with('success', 'Hour rejected.');
    }
}
