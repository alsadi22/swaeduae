<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class VolunteerDashboardController extends Controller
{
    private function hasCols(string $table, array $cols): bool
    {
        if (!Schema::hasTable($table)) return false;
        foreach ($cols as $c) if (!Schema::hasColumn($table, $c)) return false;
        return true;
    }

    public function dashboard(Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        // Total minutes
        $totalMinutes = 0;
        if ($this->hasCols('volunteer_hours', ['minutes','user_id'])) {
            $totalMinutes = (int) DB::table('volunteer_hours')->where('user_id',$user->id)->sum('minutes');
        } elseif ($this->hasCols('attendance', ['user_id','check_in','check_out'])) {
            $row = DB::table('attendance')
                ->select(DB::raw('SUM(TIMESTAMPDIFF(MINUTE, check_in, check_out)) as m'))
                ->where('user_id',$user->id)->first();
            $totalMinutes = (int) (($row && $row->m) ? $row->m : 0);
        }
        $totalHoursFmt = sprintf('%d:%02d', intdiv($totalMinutes,60), $totalMinutes%60);
        $totalHoursFloor = intdiv($totalMinutes,60);

        // Opportunity map (id -> title) if available
        $oppTitle = [];
        if ($this->hasCols('opportunities', ['id','title'])) {
            $oppTitle = DB::table('opportunities')->pluck('title','id')->all();
        }

        // Upcoming / Past
        $now = Carbon::now();
        $upcoming = collect(); $past = collect();
        if (Schema::hasTable('opportunities')) {
            $q = DB::table('opportunities')->whereNull('deleted_at');
            $dateCol = null;
            foreach (['start_date','starts_at','event_date','date','when'] as $c) {
                if (Schema::hasColumn('opportunities', $c)) { $dateCol = $c; break; }
            }
            if ($dateCol) {
                $opps = $q->select('id','title','slug', $dateCol.' as d','location')->orderBy($dateCol,'asc')->limit(80)->get();
                $upcoming = $opps->filter(fn($o)=> $o->d && Carbon::parse($o->d)->gte($now))->take(5)->values();
                $past     = $opps->filter(fn($o)=> $o->d && Carbon::parse($o->d)->lt($now))->sortByDesc('d')->take(5)->values();
            }
        }

        // Certificates
        $certs = collect();
        if (Schema::hasTable('certificates') && Schema::hasColumn('certificates','user_id')) {
            $sel = array_values(array_filter(['id','code','title','file_path','issued_at','user_id','opportunity_id'], fn($c)=> Schema::hasColumn('certificates', $c)));
            $order = Schema::hasColumn('certificates','issued_at') ? 'issued_at' : 'id';
            $certs = DB::table('certificates')->select($sel)->where('user_id',$user->id)->orderBy($order,'desc')->limit(10)->get();
        }

        // Applications
        $applications = collect();
        foreach ([['applications'],['volunteer_applications'],['opportunity_applications'],['opportunity_user']] as $cand) {
            $tbl = $cand[0];
            if ($this->hasCols($tbl, ['user_id','opportunity_id'])) {
                $select = ['id','user_id','opportunity_id'];
                if (Schema::hasColumn($tbl,'status')) $select[]='status';
                if (Schema::hasColumn($tbl,'created_at')) $select[]='created_at';
                $applications = DB::table($tbl)->select($select)->where('user_id',$user->id)->orderBy('id','desc')->limit(10)->get()
                    ->map(function($r) use ($oppTitle){
                        $r->status = $r->status ?? 'pending';
                        $r->created_at = $r->created_at ?? null;
                        $r->op_title = (isset($r->opportunity_id) && isset($oppTitle[$r->opportunity_id]))
                            ? $oppTitle[$r->opportunity_id] : null;
                        return $r;
                    });
                break;
            }
        }

        // Attendance
        $attendance = collect();
        if ($this->hasCols('attendance', ['user_id','opportunity_id','check_in'])) {
            $sel = ['id','user_id','opportunity_id','check_in'];
            if (Schema::hasColumn('attendance','check_out')) $sel[]='check_out';
            $attendance = DB::table('attendance')->select($sel)->where('user_id',$user->id)->orderBy('id','desc')->limit(10)->get()
                ->map(function($r) use ($oppTitle){
                    $in = isset($r->check_in) ? Carbon::parse($r->check_in) : null;
                    $out = (isset($r->check_out) && $r->check_out) ? Carbon::parse($r->check_out) : null;
                    $r->minutes = ($in && $out) ? $in->diffInMinutes($out,false) : null;
                    $r->op_title = (isset($r->opportunity_id) && isset($oppTitle[$r->opportunity_id]))
                        ? $oppTitle[$r->opportunity_id] : null;
                    return $r;
                });
        }

        // Badges
        $badges = [];
        if ($totalHoursFloor >= 100) $badges[] = ['label'=>'Gold Volunteer','class'=>'warning'];
        if ($totalHoursFloor >= 50)  $badges[] = ['label'=>'Silver Volunteer','class'=>'secondary'];
        if ($totalHoursFloor >= 10)  $badges[] = ['label'=>'Bronze Volunteer','class'=>'bronze'];

        return view('volunteer.dashboard', compact(
            'user','totalMinutes','totalHoursFmt','upcoming','past','certs','badges','applications','attendance'
        ));
    }
}
