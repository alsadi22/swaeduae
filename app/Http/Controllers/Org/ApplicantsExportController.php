<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicantsExportController extends Controller
{
    public function csv(Request $request)
    {
        $orgId = DB::table('organizations')->where('owner_user_id', Auth::id())->value('id');
        abort_unless($orgId, 403);

        $status = $request->query('status'); // approved|pending|declined|waitlist
        $oppId  = $request->query('opportunity_id');
        $from   = $request->query('date_from');
        $to     = $request->query('date_to');

        $oppIds = DB::table('opportunities')->where('organization_id',$orgId)->pluck('id');
        $q = DB::table('applications as a')
            ->join('users as u','u.id','=','a.user_id')
            ->join('opportunities as o','o.id','=','a.opportunity_id')
            ->selectRaw('a.id, a.status, a.created_at as applied_at, u.name as user_name, u.email, o.id as opportunity_id, o.title as opportunity_title');

        $q->whereIn('a.opportunity_id',$oppIds);

        if ($status) $q->where('a.status', $status);
        if ($oppId)  $q->where('a.opportunity_id', (int)$oppId);
        if ($from)   $q->whereDate('a.created_at', '>=', $from);
        if ($to)     $q->whereDate('a.created_at', '<=', $to);

        $rows = $q->orderBy('a.created_at','desc')->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="applicants-export.csv"',
        ];

        return new StreamedResponse(function() use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Application ID','Status','Applied At','User Name','Email','Opportunity ID','Opportunity Title']);
            foreach ($rows as $r) {
                fputcsv($out, [(string)$r->id, $r->status, (string)$r->applied_at, $r->user_name, $r->email, (string)$r->opportunity_id, $r->opportunity_title]);
            }
            fclose($out);
        }, 200, $headers);
    }
}
