<?php

namespace App\Http\Controllers\My;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CertificatesController extends Controller
{
    public function index(Request $request)
    {
        $u = $request->user();
        if (!Schema::hasTable('certificates')) { return view('my/certificates', ['rows'=>collect()]); }
        $rows = DB::table('certificates')->where('user_id',$u->id)->orderByDesc('id')->paginate(15);
        return view('my/certificates', compact('rows'));
    }

    public function download(Request $request, $id)
    {
        $u = $request->user();
        $row = DB::table('certificates')->where('id',$id)->where('user_id',$u->id)->first();
        abort_unless($row, 404);
        $path = public_path($row->file_path ?? '');
        if (!$path || !is_file($path)) {
            return back()->with('error', __('File not found.'));
        }
        return response()->download($path, basename($path));
    }
}
