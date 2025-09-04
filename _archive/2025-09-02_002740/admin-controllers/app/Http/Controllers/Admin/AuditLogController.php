<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $r)
    {
        $q = trim((string)$r->get('q',''));
        $logs = AuditLog::with('user')
            ->when($q !== '', function($w) use($q){
                $w->where('action','like',"%{$q}%")
                  ->orWhere('route_name','like',"%{$q}%")
                  ->orWhere('path','like',"%{$q}%");
            })
            ->orderBy('id','desc')
            ->paginate(25)->appends($r->query());

        return view('admin.audit.index', compact('logs','q'));
    }

    public function export()
    {
        $fh = fopen('php://temp','w+');
        fputcsv($fh, ['id','when','user_id','action','method','route','path','model','model_id','status','ip']);
        AuditLog::orderBy('id','desc')->chunk(1000, function($chunk) use($fh){
            foreach ($chunk as $a) {
                fputcsv($fh, [
                    $a->id, optional($a->created_at)->toDateTimeString(), $a->user_id, $a->action,
                    $a->method, $a->route_name, $a->path, $a->model_type, $a->model_id,
                    $a->meta['status'] ?? null, $a->meta['ip'] ?? null
                ]);
            }
        });
        rewind($fh);
        return response(stream_get_contents($fh),200,[
            'Content-Type'=>'text/csv',
            'Content-Disposition'=>'attachment; filename="audit_logs.csv"'
        ]);
    }
}
