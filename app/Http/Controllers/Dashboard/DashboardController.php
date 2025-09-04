<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Event;
use App\Models\VolunteerHour;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('dashboard.index', [
            'certCount' => Certificate::where('user_id', $user->id)->count(),
            'hours' => VolunteerHour::where('user_id', $user->id)->sum('hours'),
            'upcoming' => Event::whereHas('registrations', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('date', '>=', now())->count(),
            'badges' => ['Top Volunteer', 'Community Helper'],
            'volChart' => ['labels'=>[], 'data'=>[]]
        ]);
    }
}
