<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventCheckin;
use Illuminate\Http\Request;

class AttendanceDashboardController extends Controller
{
    public function index()
    {
        $checkins = EventCheckin::with('user', 'event')->latest()->paginate(20);
        return view('attendance.dashboard', compact('checkins'));
    }
}
