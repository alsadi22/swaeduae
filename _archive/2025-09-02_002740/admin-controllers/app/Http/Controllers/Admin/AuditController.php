<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $q = DB::table('audit_logs')->orderByDesc('created_at');

        if ($s = $request->query('q')) {
            $q->where(function($w) use ($s) {
                $w->where('action','like',"%$s%")
                  ->orWhere('entity_type','like',"%$s%")
                  ->orWhere('note','like',"%$s%");
            });
        }
        if ($t = $request->query('entity_type')) $q->where('entity_type', $t);
        if ($eid = $request->query('entity_id')) $q->where('entity_id', (int)$eid);
        if ($aid = $request->query('actor_id'))  $q->where('actor_id', (int)$aid);
        if ($from = $request->query('date_from')) $q->whereDate('created_at','>=',$from);
        if ($to   = $request->query('date_to'))   $q->whereDate('created_at','<=',$to);

        $logs = $q->paginate(50)->appends($request->query());

        $types = DB::table('audit_logs')->select('entity_type')->distinct()->orderBy('entity_type')->pluck('entity_type');

        return view('admin.audit.index', compact('logs','types'));
    }

    public function export(Request $request)
    {
        $q = DB::table('audit_logs')->orderByDesc('created_at');

        if ($s = $request->query('q')) {
            $q->where(function($w) use ($s) {
                $w->where('action','like',"%$s%")
                  ->orWhere('entity_type','like',"%$s%")
                  ->orWhere('note','like',"%$s%");
            });
        }
        if ($t = $request->query('entity_type')) $q->where('entity_type', $t);
        if ($eid = $request->query('entity_id')) $q->where('entity_id', (int)$eid);
        if ($aid = $request->query('actor_id'))  $q->where('actor_id', (int)$aid);
        if ($from = $request->query('date_from')) $q->whereDate('created_at','>=',$from);
        if ($to   = $request->query('date_to'))   $q->whereDate('created_at','<=',$to);

        $rows = $q->limit(5000)->get();
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="audit-export.csv"',
        ];

        return new StreamedResponse(function() use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID','Actor ID','Action','Entity Type','Entity ID','Note','IP','User Agent','Created At']);
            foreach ($rows as $r) {
                fputcsv($out, [(string)$r->id,(string)$r->actor_id,$r->action,$r->entity_type,(string)$r->entity_id,$r->note,$r->ip,$r->user_agent,(string)$r->created_at]);
            }
            fclose($out);
        }, 200, $headers);
    }
}
