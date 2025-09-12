<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class DashboardCompatController extends Controller
{
    public function __invoke(Request $request){ return $this->index($request); }

    public function index(Request $request)
    {
        $counts = [
            'users'         => $this->safeCount('users'),
            'events'        => $this->safeCount('events'),
            'opportunities' => $this->safeCount('opportunities'),
            'applications'  => $this->safeCount('opportunity_applications'),
            'qr_scans'      => $this->safeCount('qr_scans'),
        ];

        $totals = [
            'totalOpportunities' => $counts['opportunities'],
            'totalVolunteers'    => $this->safeVolunteerCount(),
            'totalOrganizations' => $this->safeOrganizationCount(),
            'totalHours'         => $this->safeHoursSum(),
        ];

        // --- Recent opportunities with compatibility aliases ---
        $recentOpps = $this->recentOppsCompat(6);
        $op = $recentOpps->first();

        $data = array_merge($counts, $totals, compact('recentOpps','op'));

        try {
            // Force-render so any blade error is caught here
            $html = view('admin.dashboard', $data)->render();
            return response($html);
        } catch (\Throwable $e) {
            Log::error('ADMIN_DASHBOARD_LEGACY_FAIL', [
                'm'=>$e->getMessage(),'f'=>$e->getFile(),'l'=>$e->getLine()
            ]);
            $error = ['m'=>$e->getMessage(),'f'=>$e->getFile(),'l'=>$e->getLine()];
            return view('admin.dashboard_compat_fallback', [
                'counts' => $counts, 'error' => $error,
            ]);
        }
    }

    private function recentOppsCompat(int $limit)
    {
        try {
            if (!Schema::hasTable('opportunities')) return collect();

            // Create legacy-friendly aliases:
            // - title
            // - start_date  (try many common names)
            // - end_date    (try many common names)
            $rows = DB::table('opportunities')
                ->select([
                    'id',
                    DB::raw("COALESCE(title, name, subject)           as title"),
                    DB::raw("COALESCE(start_date, starts_at, starts_on, start_time, event_start, date, created_at) as start_date"),
                    DB::raw("COALESCE(end_date,   ends_at,   ends_on,   end_time,   event_end,   deadline,  created_at) as end_date"),
                    'created_at',
                ])
                ->orderByDesc('id')
                ->limit($limit)
                ->get();

            // Ensure properties exist even if NULL
            return $rows->map(function ($r) {
                if (!isset($r->title))      $r->title = null;
                if (!isset($r->start_date)) $r->start_date = null;
                if (!isset($r->end_date))   $r->end_date = null;
                return $r;
            });
        } catch (\Throwable $e) {
            return collect();
        }
    }

    private function safeCount(string $table): int {
        try { return Schema::hasTable($table) ? DB::table($table)->count() : 0; }
        catch (\Throwable $e) { return 0; }
    }
    private function safeVolunteerCount(): int {
        try {
            if (!Schema::hasTable('users')) return 0;
            if (Schema::hasColumn('users','role')) {
                return DB::table('users')->where('role','volunteer')->count();
            }
            if (Schema::hasColumn('users','is_admin')) {
                return DB::table('users')->where('is_admin',0)->count();
            }
            return DB::table('users')->count();
        } catch (\Throwable $e) { return 0; }
    }
    private function safeOrganizationCount(): int {
        try {
            if (Schema::hasTable('organizations')) return DB::table('organizations')->count();
            if (Schema::hasTable('users') && Schema::hasColumn('users','role')) {
                return DB::table('users')->whereIn('role',['organization','org'])->count();
            }
            return 0;
        } catch (\Throwable $e) { return 0; }
    }
    private function safeHoursSum(): float {
        try {
            if (Schema::hasTable('volunteer_hours')) {
                return (float) DB::table('volunteer_hours')->sum('hours');
            }
            if (Schema::hasTable('attendances')) {
                if (Schema::hasColumn('attendances','minutes')) {
                    $m = (int) DB::table('attendances')->sum('minutes');
                    return round($m/60, 2);
                }
                if (Schema::hasColumn('attendances','hours')) {
                    return (float) DB::table('attendances')->sum('hours');
                }
            }
            return 0.0;
        } catch (\Throwable $e) { return 0.0; }
    }
}
