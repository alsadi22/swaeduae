<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:org']);
    }

    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');
        $oppId    = $request->input('opportunity_id');

        $rows = $this->buildHoursQuery($dateFrom, $dateTo, $oppId)->limit(100)->get(); // small preview

        $opportunities = $this->tableExists('opportunities')
            ? DB::table('opportunities')->select('id','title')->orderBy('title')->get()
            : collect();

        return view('org.reports.index', [
            'rows' => $rows,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'oppId' => $oppId,
            'opportunities' => $opportunities,
        ]);
    }

    public function exportHoursCsv(Request $request): StreamedResponse
    {
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');
        $oppId    = $request->input('opportunity_id');

        $query = $this->buildHoursQuery($dateFrom, $dateTo, $oppId);

        $filename = 'hours_report_' . now('Asia/Dubai')->format('Ymd_His') . '.csv';

        return response()->streamDownload(function() use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Volunteer Name','Volunteer Email','Opportunity','Hours','Minutes','Entries']);
            $query->orderBy('volunteer_name')->chunk(1000, function($chunk) use ($out) {
                foreach ($chunk as $r) {
                    $hours = round(($r->total_minutes ?? 0) / 60, 2);
                    fputcsv($out, [
                        $r->volunteer_name,
                        $r->volunteer_email,
                        $r->opportunity_title,
                        $hours,
                        (int)($r->total_minutes ?? 0),
                        (int)($r->entries ?? 0),
                    ]);
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    // ---- Query builder that is schema-aware ----
    private function buildHoursQuery(?string $dateFrom, ?string $dateTo, $oppId)
    {
        $tz = 'Asia/Dubai';

        if ($this->tableExists('volunteer_hours') && $this->columnExists('volunteer_hours','minutes')) {
            $q = DB::table('volunteer_hours as vh')
                ->selectRaw("
                    COALESCE(u.name,'Unknown') as volunteer_name,
                    u.email as volunteer_email,
                    COALESCE(o.title, CONCAT('Opp #', vh.opportunity_id)) as opportunity_title,
                    SUM(COALESCE(vh.minutes,0)) as total_minutes,
                    COUNT(*) as entries
                ")
                ->leftJoin('users as u','u.id','=','vh.user_id')
                ->leftJoin('opportunities as o','o.id','=','vh.opportunity_id')
                ->groupBy('u.name','u.email','o.title','vh.opportunity_id');

            if ($dateFrom) $q->whereDate('vh.created_at','>=',$dateFrom);
            if ($dateTo)   $q->whereDate('vh.created_at','<=',$dateTo);
            if ($oppId)    $q->where('vh.opportunity_id',$oppId);

            return $q;
        }

        // Fallback: derive minutes from attendances if hours table not present
        $q = DB::table('attendances as a')
            ->selectRaw("
                COALESCE(u.name,'Unknown') as volunteer_name,
                u.email as volunteer_email,
                COALESCE(o.title, CONCAT('Opp #', a.opportunity_id)) as opportunity_title,
                SUM(TIMESTAMPDIFF(MINUTE, a.checked_in_at, a.checked_out_at)) as total_minutes,
                COUNT(*) as entries
            ")
            ->leftJoin('users as u','u.id','=','a.user_id')
            ->leftJoin('opportunities as o','o.id','=','a.opportunity_id')
            ->whereNotNull('a.checked_in_at')
            ->whereNotNull('a.checked_out_at')
            ->groupBy('u.name','u.email','o.title','a.opportunity_id');

        if ($dateFrom) $q->whereDate('a.checked_in_at','>=',$dateFrom);
        if ($dateTo)   $q->whereDate('a.checked_out_at','<=',$dateTo);
        if ($oppId)    $q->where('a.opportunity_id',$oppId);

        return $q;
    }

    // ---- schema helpers ----
    private function tableExists(string $table): bool
    {
        try { return DB::getSchemaBuilder()->hasTable($table); } catch (\Throwable $e) { return false; }
    }
    private function columnExists(string $table, string $col): bool
    {
        try { return DB::getSchemaBuilder()->hasColumn($table,$col); } catch (\Throwable $e) { return false; }
    }
}
