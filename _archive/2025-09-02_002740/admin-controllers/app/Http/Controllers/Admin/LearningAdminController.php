<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LearningAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q',''));
        $rows = DB::table('learning_requests')
            ->when($q !== '', function($qry) use ($q) {
                $qry->where('title','like',"%{$q}%")
                    ->orWhere('details','like',"%{$q}%")
                    ->orWhere('status','like',"%{$q}%");
            })
            ->orderByDesc('id')->paginate(20)->withQueryString();

        return view('admin.learning.index', compact('rows','q'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['status'=>'required|in:pending,approved,rejected']);
        DB::table('learning_requests')->where('id',$id)->update([
            'status'=>$request->status,
            'updated_at'=>now(),
        ]);
        return redirect()->route('admin.learning.index')->with('status', __('Updated'));
    }
}
