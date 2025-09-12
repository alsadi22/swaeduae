<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventVolunteersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:org']);
    }

    public function index($opportunityId, Request $request)
    {
        $rows = $this->fetchRows($opportunityId);

        $opportunity = $this->tableExists('opportunities')
            ? DB::table('opportunities')->where('id',$opportunityId)->first()
            : null;

        if ($request->boolean('partial')) {
            return view('org.events._volunteers_table', [
                'rows' => ($rows ?? []),
                'opportunityId' => $opportunityId
            ]);
        }

        return view('org.events.volunteers', [
            'rows' => ($rows ?? []),
            'opportunityId' => $opportunityId,
            'opportunity' => $opportunity
        ]);
    }

    public function exportCsv($opportunityId): StreamedResponse
    {
        $rows = $this->fetchRows($opportunityId);

        $filename = 'event_'.$opportunityId.'_volunteers_' . now('Asia/Dubai')->format('Ymd_His') . '.csv';
        return response()->streamDownload(function() use ($rows) {
            $out = fopen('php://output','w');
            fputcsv($out, ['Volunteer Name','Email','Applied','Approved','Attended','Total Minutes']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->name,
                    $r->email,
                    $r->applied ? 'yes':'no',
                    $r->approved ? 'yes':'no',
                    $r->attended ? 'yes':'no',
                    (int)($r->minutes ?? 0),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type'=>'text/csv']);
    }

    private function fetchRows($opportunityId)
    {
        // one row per ATTENDANCE (has attendance_id for inline updates)
        return DB::table('attendances as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->selectRaw("
                a.id as attendance_id,
                u.id as user_id,
                u.name,
                u.email,
                CASE WHEN EXISTS (
                    SELECT 1 FROM applications ap
                    WHERE ap.user_id = u.id AND ap.opportunity_id = a.opportunity_id
                ) THEN 1 ELSE 0 END as applied,
                CASE WHEN EXISTS (
                    SELECT 1 FROM applications ap
                    WHERE ap.user_id = u.id AND ap.opportunity_id = a.opportunity_id
                      AND (ap.status IN ('approved','accepted') OR ap.is_approved = 1)
                ) THEN 1 ELSE 0 END as approved,
                CASE WHEN (a.checked_in_at IS NOT NULL OR a.check_in_at IS NOT NULL OR a.checkin_at IS NOT NULL)
                     THEN 1 ELSE 0 END as attended,
                COALESCE(
                    a.minutes,
                    IFNULL(a.hours,0)*60,
                    TIMESTAMPDIFF(
                        MINUTE,
                        COALESCE(a.check_in_at, a.checked_in_at, a.checkin_at),
                        COALESCE(a.check_out_at, a.checked_out_at, a.checkout_at)
                    )
                ) as minutes
            ")
            ->where('a.opportunity_id', $opportunityId)
            ->orderBy('u.name')
            ->get();
    }

    private function tableExists(string $table): bool
    {
        try { return DB::getSchemaBuilder()->hasTable($table); } catch (\Throwable $e) { return false; }
    }
}
