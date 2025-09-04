<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HoursReportController extends Controller
{
    public function showAll()
    {
        $hasTable = Schema::hasTable('hours');
        $rows = collect();
        if ($hasTable) {
            $rows = DB::table('hours')->select('id','user_id','opportunity_id','hours','status','created_at')
                     ->orderByDesc('created_at')->paginate(20);
        }
        return view('admin.hours.index', compact('rows','hasTable'));
    }

    public function bulkApprove(Request $r)
    {
        $ids = array_filter((array) $r->input('ids', []), 'is_numeric');
        if (Schema::hasTable('hours') && $ids) {
            DB::table('hours')->whereIn('id',$ids)->update(['status'=>'approved']);
            return back()->with('ok','Approved '.count($ids).' records');
        }
        return back()->with('err','No changes made');
    }

    public function exportCsv(): StreamedResponse
    {
        if (!Schema::hasTable('hours')) {
            return response()->streamDownload(function(){}, 'hours.csv');
        }
        $rows = DB::table('hours')->select('id','user_id','opportunity_id','hours','status','created_at')
                 ->orderByDesc('created_at')->cursor();

        return response()->streamDownload(function() use ($rows){
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id','user_id','opportunity_id','hours','status','created_at']);
            foreach ($rows as $r) {
                fputcsv($out, [(int)$r->id,(int)$r->user_id,(int)$r->opportunity_id,(float)$r->hours,$r->status,$r->created_at]);
            }
            fclose($out);
        }, 'hours.csv', ['Content-Type' => 'text/csv']);
    }
}
