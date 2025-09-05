<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrganizationsController extends Controller
{
    public function index()
    {
        $hasTable = Schema::hasTable('organizations');

        $pending = collect();
        $active  = collect();

        if ($hasTable) {
            // Use safe selects even if some columns don't exist
            $cols = Schema::getColumnListing('organizations');
            $select = array_intersect(['id','name','email','status','created_at'], $cols) ?: ['*'];

            $pending = DB::table('organizations')
                ->select($select)
                ->when(in_array('status',$cols), fn($q) => $q->where('status','pending'))
                ->orderByDesc(in_array('created_at',$cols) ? 'created_at' : 'id')
                ->limit(100)
                ->get();

            $active = DB::table('organizations')
                ->select($select)
                ->when(in_array('status',$cols), fn($q) => $q->where('status','active'))
                ->orderBy(in_array('name',$cols) ? 'name' : 'id')
                ->limit(100)
                ->get();
        }

        return view('admin.organizations.index', compact('hasTable','pending','active'));
    }

    public function approve(int $id)
    {
        if (Schema::hasTable('organizations') && Schema::hasColumn('organizations','status')) {
            DB::table('organizations')->where('id', $id)->update(['status' => 'active']);
        }
        return back()->with('ok','Organization approved');
    }

    public function suspend(int $id)
    {
        if (Schema::hasTable('organizations') && Schema::hasColumn('organizations','status')) {
            DB::table('organizations')->where('id', $id)->update(['status' => 'suspended']);
        }
        return back()->with('ok','Organization suspended');
    }
}
