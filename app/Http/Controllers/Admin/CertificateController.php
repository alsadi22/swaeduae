<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function index()
    {
        $hasTable = Schema::hasTable('certificates');
        $rows = collect();
        if ($hasTable) {
            $rows = DB::table('certificates')->orderByDesc('issued_at')->paginate(15);
        }
        return view('admin.certificates.index', compact('rows','hasTable'));
    }

    public function issue(Request $r)
    {
        if (!Schema::hasTable('certificates')) return back()->with('err','No table');
        $data = $r->validate(['user_id'=>'required|integer','hours'=>'nullable|numeric']);
        $uuid = (string) Str::uuid();
        $serial = strtoupper(Str::random(10));
        DB::table('certificates')->insert([
            'uuid'=>$uuid,'serial'=>$serial,'user_id'=>$data['user_id'],
            'hours'=>$data['hours'] ?? 0,'issued_at'=>now(),
        ]);
        return back()->with('ok',"Issued $serial");
    }

    public function reissue($id)
    {
        if (Schema::hasTable('certificates')) {
            DB::table('certificates')->where('id',$id)->update(['issued_at'=>now()]);
        }
        return back()->with('ok','Reissued');
    }
}
