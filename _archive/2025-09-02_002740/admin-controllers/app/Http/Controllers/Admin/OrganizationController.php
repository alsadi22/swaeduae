<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q',''));
        $exists = Schema::hasTable('organizations');
        $rows = $exists ? DB::table('organizations')
            ->when($q !== '', fn($qry)=>$qry->where(fn($w)=>$w
                ->where('name','like',"%{$q}%")
                ->orWhere('email','like',"%{$q}%")
                ->orWhere('phone','like',"%{$q}%")))
            ->orderByDesc('id')->paginate(15)->withQueryString() : null;

        return view('admin.organizations.index', compact('rows','exists','q'));
    }

    public function create() { return view('admin.organizations.create'); }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|max:190',
            'email'=>'nullable|email|max:190',
            'phone'=>'nullable|max:190',
            'website'=>'nullable|url|max:190',
        ]);
        DB::table('organizations')->insert([
            'name'=>$request->name, 'email'=>$request->email, 'phone'=>$request->phone,
            'website'=>$request->website, 'created_at'=>now(), 'updated_at'=>now(),
        ]);
        return redirect()->route('admin.organizations.index')->with('status', __('Organization created'));
    }

    public function edit($id)
    {
        $org = DB::table('organizations')->where('id',$id)->first();
        abort_unless($org, 404);
        return view('admin.organizations.edit', compact('org'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'=>'required|max:190',
            'email'=>'nullable|email|max:190',
            'phone'=>'nullable|max:190',
            'website'=>'nullable|url|max:190',
        ]);
        DB::table('organizations')->where('id',$id)->update([
            'name'=>$request->name, 'email'=>$request->email, 'phone'=>$request->phone,
            'website'=>$request->website, 'updated_at'=>now(),
        ]);
        return redirect()->route('admin.organizations.index')->with('status', __('Organization updated'));
    }

    public function destroy($id)
    {
        DB::table('organizations')->where('id',$id)->delete();
        return redirect()->route('admin.organizations.index')->with('status', __('Organization deleted'));
    }
}
