<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventApplication;
use Illuminate\Http\Request;

class VolunteerHoursController extends Controller
{
    public function index()
    {
        $applications = EventApplication::with('user', 'event')->paginate(20);
        return view('admin.volunteer_hours.index', compact('applications'));
    }

    public function approve(Request $request, EventApplication $application)
    {
        $request->validate([
            'hours_logged' => 'required|numeric|min:0',
            'approved' => 'required|boolean',
        ]);
        $application->hours_logged = $request->hours_logged;
        $application->approved = $request->approved;
        $application->save();

        return redirect()->back()->with('success', 'Volunteer hours updated.');
    }
}
