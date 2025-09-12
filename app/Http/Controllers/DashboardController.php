<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect('/signin');

        // if admin, send to admin dashboard
        try {
            if ((method_exists($user, 'hasRole') && $user->hasRole('admin')) || (!empty($user->is_admin))) {
                return redirect('/admin/dashboard');
            }
        } catch (\Throwable $e) {}

        $totalMinutes = 0;
        $eventsCount  = 0;

        // time
        try {
            if (Schema::hasTable('volunteer_hours')) {
                if (Schema::hasColumn('volunteer_hours', 'minutes')) {
                    $totalMinutes = (int) DB::table('volunteer_hours')->where('user_id', $user->id)->sum('minutes');
                } else {
                    $hours = (int) DB::table('volunteer_hours')->where('user_id', $user->id)->sum('hours');
                    $totalMinutes = $hours * 60;
                }
            }
        } catch (\Throwable $e) {}

        // events count
        try {
            if (Schema::hasTable('attendances')) {
                $eventsCount = (int) DB::table('attendances')->where('user_id', $user->id)->distinct()->count('opportunity_id');
            } elseif (Schema::hasTable('event_user')) {
                $eventsCount = (int) DB::table('event_user')->where('user_id', $user->id)->distinct()->count('event_id');
            }
        } catch (\Throwable $e) {}

        // certificates
        $latestCert = null;
        $recentCerts = collect();
        try {
            if (Schema::hasTable('certificates')) {
                $q = DB::table('certificates')->where('user_id', $user->id)
                    ->orderByRaw('COALESCE(issued_at, created_at) DESC');
                $latestCert  = $q->first();
                $recentCerts = $q->limit(3)->get();
            }
        } catch (\Throwable $e) {}

        // month aggregates
        $hoursThisMonth = 0; $hoursLastMonth = 0;
        try {
            if (Schema::hasTable('volunteer_hours')) {
                $dateCol = Schema::hasColumn('volunteer_hours','worked_at') ? 'worked_at' : 'created_at';
                $thisQ = DB::table('volunteer_hours')->where('user_id',$user->id)
                        ->whereBetween($dateCol,[now()->startOfMonth(),now()->endOfMonth()]);
                $lastQ = DB::table('volunteer_hours')->where('user_id',$user->id)
                        ->whereBetween($dateCol,[now()->subMonth()->startOfMonth(),now()->subMonth()->endOfMonth()]);
                $hoursThisMonth = (int)(((Schema::hasColumn('volunteer_hours','minutes')?$thisQ->sum('minutes'):$thisQ->sum('hours')*60))/60);
                $hoursLastMonth = (int)(((Schema::hasColumn('volunteer_hours','minutes')?$lastQ->sum('minutes'):$lastQ->sum('hours')*60))/60);
            }
        } catch (\Throwable $e) {}

        // hours by month last 6
        $hoursByMonth = [];
        try {
            if (Schema::hasTable('volunteer_hours')) {
                $dateCol = Schema::hasColumn('volunteer_hours','worked_at') ? 'worked_at' : 'created_at';
                $rows = DB::table('volunteer_hours')
                    ->selectRaw("DATE_FORMAT($dateCol, '%Y-%m') as ym")
                    ->selectRaw((Schema::hasColumn('volunteer_hours','minutes')?'SUM(minutes)/60':'SUM(hours)').' as h')
                    ->where('user_id',$user->id)
                    ->where($dateCol, '>=', now()->subMonths(5)->startOfMonth())
                    ->groupBy('ym')->orderBy('ym')->get();
                for($i=5;$i>=0;$i--){
                    $ym = now()->subMonths($i)->format('Y-m');
                    $f = $rows->firstWhere('ym',$ym);
                    $hoursByMonth[$ym] = $f?(int)round($f->h):0;
                }
            }
        } catch (\Throwable $e) {}

        // upcoming/ongoing
        $upcoming = collect();
        try {
            if (Schema::hasTable('attendances') && Schema::hasTable('opportunities')) {
                $upcoming = DB::table('attendances as a')
                    ->join('opportunities as o','o.id','=','a.opportunity_id')
                    ->where('a.user_id',$user->id)
                    ->when(Schema::hasColumn('opportunities','start_at'), fn($q)=>$q->where('o.start_at','>=',now()->subDay())->orderBy('o.start_at'))
                    ->selectRaw('o.id, o.title, o.location, o.start_at, o.end_at, a.status')->limit(5)->get();
            } elseif (Schema::hasTable('event_user') && Schema::hasTable('events')) {
                $upcoming = DB::table('event_user as eu')
                    ->join('events as e','e.id','=','eu.event_id')
                    ->where('eu.user_id',$user->id)
                    ->when(Schema::hasColumn('events','start_at'), fn($q)=>$q->where('e.start_at','>=',now()->subDay())->orderBy('e.start_at'))
                    ->selectRaw('e.id, e.name as title, e.venue as location, e.start_at, e.end_at, eu.status')->limit(5)->get();
            }
        } catch (\Throwable $e) {}

        // verification / profile completion
        $isVerified = method_exists($user,'hasVerifiedEmail') ? $user->hasVerifiedEmail() : !empty($user->email_verified_at);

        $fields = ['phone','nationality','gender','dob','emirate','city','emirates_id','education','experience','languages','skills','interests','availability','bio','photo_path'];
        $filled=0; $total=0;
        foreach($fields as $f){ if(Schema::hasColumn('users',$f)){ $total++; if(!empty($user->{$f})) $filled++; } }
        $profilePercent = $total ? (int)round($filled*100/$total) : 0;

        $totalHours = intdiv($totalMinutes,60);
        $badge = $totalHours >= 100 ? 'Gold Volunteer' : ($totalHours >= 50 ? 'Silver Volunteer' : ($totalHours >= 10 ? 'Bronze Volunteer' : 'New Volunteer'));

        return view('volunteer.dashboard', compact(
            'user','totalMinutes','totalHours','eventsCount','latestCert','recentCerts',
            'hoursThisMonth','hoursLastMonth','hoursByMonth','upcoming','isVerified','profilePercent','badge'
        ));
    }
}
