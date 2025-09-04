<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicantsController extends Controller
{
    public function index(Request $r)
    {
        $hasTable = Schema::hasTable('applications');
        $status = in_array($r->get('status'), ['pending','approved','declined']) ? $r->get('status') : 'pending';

        $rows = collect();
        if ($hasTable) {
            $q = DB::table('applications')->select('id','user_id','opportunity_id','status','created_at');
            if ($status) $q->where('status', $status);
            $rows = $q->orderByDesc('created_at')->paginate(20)->withQueryString();
        }

        return view('admin.applicants.index', compact('rows','status','hasTable'));
    }

    public function approve($id)
    {
        if (Schema::hasTable('applications')) {
            DB::table('applications')->where('id',$id)->update(['status'=>'approved']);
        }
        return back()->with('ok','Approved');
    }

    public function decline($id)
    {
        if (Schema::hasTable('applications')) {
            DB::table('applications')->where('id',$id)->update(['status'=>'declined']);
        }
        return back()->with('ok','Declined');
    }

    public function bulk(Request $r)
    {
        $ids = array_filter((array) $r->input('ids', []), 'is_numeric');
        $action = $r->input('action');
        if (Schema::hasTable('applications') && $ids && in_array($action, ['approve','decline'])) {
            DB::table('applications')->whereIn('id',$ids)->update(['status'=>$action==='approve'?'approved':'declined']);
            return back()->with('ok', ucfirst($action).'d '.count($ids).' applicants');
        }
        return back()->with('err','No changes made');
    }

    public function exportCsv(Request $r): StreamedResponse
    {
        if (!Schema::hasTable('applications')) {
            return response()->streamDownload(function(){}, 'applications.csv');
        }
        $status = in_array($r->get('status'), ['pending','approved','declined']) ? $r->get('status') : null;
        $q = DB::table('applications')->select('id','user_id','opportunity_id','status','created_at');
        if ($status) $q->where('status',$status);
        $rows = $q->orderByDesc('created_at')->cursor();

        return response()->streamDownload(function() use ($rows){
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id','user_id','opportunity_id','status','created_at']);
            foreach ($rows as $r) {
                fputcsv($out, [(int)$r->id,(int)$r->user_id,(int)$r->opportunity_id,$r->status,$r->created_at]);
            }
            fclose($out);
        }, 'applications.csv', ['Content-Type' => 'text/csv']);
    }
}
