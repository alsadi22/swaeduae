<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportsController extends Controller
{
    public function index(Request $r)
    {
        $from = $r->get('from'); $to = $r->get('to');
        $range = [$from ?: now()->subMonth()->toDateString(), $to ?: now()->toDateString()];
        $has = [
            'hours' => Schema::hasTable('hours'),
            'applications' => Schema::hasTable('applications'),
            'certificates' => Schema::hasTable('certificates'),
        ];

        $data = [
            'hours_by_org' => collect(),
            'hours_by_opportunity' => collect(),
            'apps_by_status' => collect(),
        ];

        if ($has['hours']) {
            $data['hours_by_org'] = DB::table('hours')
                ->selectRaw('opportunity_id, SUM(hours) as total')
                ->whereBetween('created_at',$range)->groupBy('opportunity_id')->limit(100)->get();
            $data['hours_by_opportunity'] = $data['hours_by_org'];
        }
        if ($has['applications']) {
            $data['apps_by_status'] = DB::table('applications')
                ->selectRaw('status, COUNT(*) as c')->whereBetween('created_at',$range)->groupBy('status')->get();
        }

        return view('admin.reports.index', compact('data','from','to','has'));
    }

    public function export(Request $r)
    {
        $table = $r->get('table','hours');
        $from = $r->get('from'); $to = $r->get('to');
        $range = [$from ?: now()->subMonth()->toDateString(), $to ?: now()->toDateString()];
        if (!Schema::hasTable($table)) return response()->streamDownload(function(){}, $table.'.csv');

        $rows = DB::table($table)->whereBetween('created_at',$range)->cursor();
        return response()->streamDownload(function() use ($rows){
            $out=fopen('php://output','w');
            $headerDone=false;
            foreach($rows as $row){
                $arr=(array)$row;
                if(!$headerDone){ fputcsv($out,array_keys($arr)); $headerDone=true; }
                fputcsv($out,$arr);
            }
            fclose($out);
        }, $table.'.csv', ['Content-Type'=>'text/csv']);
    }
}
