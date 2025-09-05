<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Extra guard: enforce auth+role at controller level too
        $this->middleware(['auth', 'role:org']);
    }

    public function index(Request $request)
    {
        // Helpers
        $today = Carbon::now('Asia/Dubai')->startOfDay();

        // ---- KPI 1: Total Volunteers Hosted (distinct attendees) ----
        $volunteersHosted = 0;
        if ($this->tableExists('attendances')) {
            $volunteersHosted = DB::table('attendances')
                ->distinct()
                ->count('user_id');
        }

        // ---- KPI 2: Total Hours (prefer volunteer_hours.minutes) ----
        $totalHours = 0.0;
        if ($this->tableExists('volunteer_hours') && $this->columnExists('volunteer_hours', 'minutes')) {
            $minutes = (int) DB::table('volunteer_hours')->sum('minutes');
            $totalHours = round($minutes / 60, 2);
        } elseif ($this->tableExists('attendances') && $this->hasInOutColumns()) {
            // Fallback: compute hours from check-in/check-out if available
            $minutes = (int) DB::table('attendances')
                ->whereNotNull('checked_in_at')
                ->whereNotNull('checked_out_at')
                ->select(DB::raw('SUM(TIMESTAMPDIFF(MINUTE, checked_in_at, checked_out_at)) as total'))
                ->value('total');
            $totalHours = round(($minutes ?? 0) / 60, 2);
        }

        // ---- KPI 3: Upcoming Opportunities (future & published-ish) ----
        $upcomingOpps = 0;
        if ($this->tableExists('opportunities')) {
            // Try to use start_date and status/published if present
            $query = DB::table('opportunities');
            if ($this->columnExists('opportunities', 'start_date')) {
                $query->whereDate('start_date', '>=', $today->toDateString());
            }
            if ($this->columnExists('opportunities', 'status')) {
                $query->whereIn('status', ['published', 'active', 'open']);
            } elseif ($this->columnExists('opportunities', 'is_published')) {
                $query->where('is_published', 1);
            }
            $upcomingOpps = (int) $query->count();
        }

        // ---- KPI 4: Certificates Issued ----
        $certificatesIssued = 0;
        if ($this->tableExists('certificates')) {
            $certificatesIssued = (int) DB::table('certificates')->count();
        }

        // ---- Chart A: Hours by Month (last 12 months) ----
        $hoursSeries = [];
        $monthLabels = [];
        if ($this->tableExists('volunteer_hours')) {
            // Try to group by created_at month
            $rows = DB::table('volunteer_hours')
                ->selectRaw("DATE_FORMAT(COALESCE(updated_at, created_at), '%Y-%m') as ym, SUM(COALESCE(minutes,0)) as total_minutes")
                ->groupBy('ym')
                ->orderBy('ym', 'asc')
                ->get();

            // Map last 12 months window
            $window = collect(range(0, 11))->map(function ($i) use ($today) {
                return $today->copy()->subMonths(11 - $i)->format('Y-m');
            })->values();

            $byYm = collect($rows)->keyBy('ym');
            foreach ($window as $ym) {
                $mins = (int) optional($byYm->get($ym))->total_minutes;
                $hoursSeries[] = round($mins / 60, 2);
                $monthLabels[] = $ym;
            }
        }

        // ---- Chart B: Applications vs Attendance per event (last 10 opps) ----
        $appAttend = [
            'labels' => [],
            'apps'   => [],
            'attend' => [],
        ];
        if ($this->tableExists('opportunities')) {
            $opps = DB::table('opportunities')
                ->select('id', 'title')
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();

            foreach ($opps as $op) {
                $label = $op->title ?? ("Opp #".$op->id);
                $apps  = $this->tableExists('applications')
                       ? (int) DB::table('applications')->where('opportunity_id', $op->id)->count()
                       : 0;

                $attend = $this->tableExists('attendances')
                        ? (int) DB::table('attendances')->where('opportunity_id', $op->id)->distinct()->count('user_id')
                        : 0;

                $appAttend['labels'][] = $label;
                $appAttend['apps'][]   = $apps;
                $appAttend['attend'][] = $attend;
            }
            // reverse so oldest→newest in charts
            $appAttend['labels'] = array_reverse($appAttend['labels']);
            $appAttend['apps']   = array_reverse($appAttend['apps']);
            $appAttend['attend'] = array_reverse($appAttend['attend']);
        }

        // ---- Recent Activity (last 5) ----
        $recentActivity = [];
        try {
            if ($this->tableExists('attendances')) {
                $att = DB::table('attendances as a')
                    ->join('users as u','u.id','=','a.user_id')
                    ->select('a.id','a.opportunity_id','u.name','a.user_id','a.updated_at','a.created_at','a.minutes')
                    ->orderByRaw('COALESCE(a.updated_at,a.created_at) DESC')
                    ->limit(5)
                    ->get();
                foreach ($att as $r) {
                    $recentActivity[] = [
                        'type' => 'attendance',
                        'who' => $r->name,
                        'minutes' => (int)($r->minutes ?? 0),
                        'opportunity_id' => (int)($r->opportunity_id ?? 0),
                        'when' => (string)($r->updated_at ?? $r->created_at),
                    ];
                }
            }
            if ($this->tableExists('applications')) {
                $apps = DB::table('applications as ap')
                    ->join('users as u','u.id','=','ap.user_id')
                    ->select('ap.id','ap.opportunity_id','u.name','ap.user_id','ap.approved','ap.updated_at','ap.created_at')
                    ->orderByRaw('COALESCE(ap.updated_at,ap.created_at) DESC')
                    ->limit(5)
                    ->get();
                foreach ($apps as $r) {
                    $recentActivity[] = [
                        'type' => 'application',
                        'who' => $r->name,
                        'status' => isset($r->approved) ? ((int)$r->approved ? 'approved':'applied') : 'applied',
                        'opportunity_id' => (int)($r->opportunity_id ?? 0),
                        'when' => (string)($r->updated_at ?? $r->created_at),
                    ];
                }
            }
            usort($recentActivity, function($a,$b){ return strcmp($b['when'] ?? '', $a['when'] ?? ''); });
            $recentActivity = array_slice($recentActivity, 0, 8);
        } catch (\Throwable $e) {
            $recentActivity = [];
        }
        return view('org.dashboard', [
            'volunteersHosted'   => $volunteersHosted,
            'totalHours'         => $totalHours,
            'upcomingOpps'       => $upcomingOpps,
            'certificatesIssued' => $certificatesIssued,
            'monthLabels'        => $monthLabels,
            'hoursSeries'        => $hoursSeries,
            'appAttend'          => $appAttend,
              'recentActivity'    => $recentActivity,
        ]);
    }

    // ---------- schema helpers ----------
    private function tableExists(string $table): bool
    {
        try {
            return DB::getSchemaBuilder()->hasTable($table);
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function columnExists(string $table, string $column): bool
    {
        try {
            return DB::getSchemaBuilder()->hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function hasInOutColumns(): bool
    {
        return $this->tableExists('attendances')
            && $this->columnExists('attendances', 'checked_in_at')
            && $this->columnExists('attendances', 'checked_out_at');
    }
}
