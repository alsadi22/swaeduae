<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $kpi = [
            'users' => Schema::hasTable('users') ? DB::table('users')->count() : 0,
            'opportunities' => Schema::hasTable('opportunities') ? DB::table('opportunities')->count() : 0,
            'applications' => Schema::hasTable('applications') ? DB::table('applications')->count() : 0,
            'hours_minutes' => Schema::hasTable('volunteer_hours') ? (int) DB::table('volunteer_hours')->sum('minutes') : 0,
            'certificates' => Schema::hasTable('certificates') ? DB::table('certificates')->count() : 0,
            'partners_new' => Schema::hasTable('partner_intake_submissions') ? DB::table('partner_intake_submissions')->where('status','new')->count() : 0,
            'audit_today' => Schema::hasTable('audit_logs') ? DB::table('audit_logs')->whereDate('created_at', date('Y-m-d'))->count() : 0,
        ];

        $latestApps = collect();
        if (Schema::hasTable('applications')) {
            $latestApps = DB::table('applications as a')
              ->leftJoin('opportunities as o','o.id','=','a.opportunity_id')
              ->select('a.*','o.title as opportunity_title')
              ->orderByDesc('a.id')->limit(10)->get();
        }

        $latestCerts = collect();
        if (Schema::hasTable('certificates')) {
            $latestCerts = DB::table('certificates')->orderByDesc('id')->limit(10)->get();
        }

        return view('admin.overview', compact('kpi','latestApps','latestCerts'));
    }
}
