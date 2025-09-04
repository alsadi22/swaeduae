<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    public function index()
    {
        $counts = [
            'users' => class_exists(\App\Models\User::class) ? \App\Models\User::count() : 0,
            'opportunities' => class_exists(\App\Models\Opportunity::class) ? \App\Models\Opportunity::count() : 0,
            'applications' => class_exists(\App\Models\Application::class) ? \App\Models\Application::count() : 0,
        ];

        // Monthly trend (users created)
        $monthlyUsers = \App\Models\User::selectRaw("DATE_FORMAT(created_at,'%Y-%m') as m, COUNT(*) c")
                          ->groupBy('m')->orderBy('m')->pluck('c','m')->toArray();

        // Optional: volunteer hours per month if table exists
        $hours = [];
        if (class_exists(\App\Models\VolunteerHour::class)) {
            $hours = \App\Models\VolunteerHour::selectRaw("DATE_FORMAT(created_at,'%Y-%m') as m, SUM(hours) s")
                    ->groupBy('m')->orderBy('m')->pluck('s','m')->toArray();
        }

        return view('admin.reports.index', [
            'counts'=>$counts,
            'monthlyUsers'=>$monthlyUsers,
            'hours'=>$hours,
        ]);
    }
}
