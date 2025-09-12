<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q',''));
        $exists = Schema::hasTable('partners');
        $rows = null;

        if ($exists) {
            $rows = DB::table('partners')
                ->when($q !== '', function ($qry) use ($q) {
                    $qry->where(function($w) use ($q) {
                        $w->where('name', 'like', "%{$q}%")
                          ->orWhere('website', 'like', "%{$q}%")
                          ->orWhere('url', 'like', "%{$q}%");
                    });
                })
                ->orderByDesc('id')
                ->paginate(15)
                ->withQueryString();
        }

        return view('admin.partners.index', compact('rows','exists','q'));
    }

    public function create()
    {
        return view('admin.partners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:190',
            'website'=>'nullable|url|max:190',
            'logo'=>'nullable|image|max:2048',
        ]);

        $logo = null;
        if ($request->hasFile('logo')) {
            @mkdir(public_path('uploads/partners'), 0775, true);
            $logo = 'uploads/partners/'.Str::random(8).'_'.time().'.'.$request->file('logo')->getClientOriginalExtension();
            $request->file('logo')->move(public_path('uploads/partners'), basename($logo));
        }

        DB::table('partners')->insert([
            'name'=>$request->name,
            'website'=>$request->website,
            'logo'=>$logo,
            'created_at'=>now(), 'updated_at'=>now(),
        ]);

        return redirect()->route('admin.partners.index')->with('status', __('Partner created'));
    }

    public function edit($id)
    {
        $partner = DB::table('partners')->where('id',$id)->first();
        abort_unless($partner, 404);
        return view('admin.partners.edit', compact('partner'));
    }

    public function update(Request $request, $id)
    {
        $partner = DB::table('partners')->where('id',$id)->first();
        abort_unless($partner, 404);

        $request->validate([
            'name'=>'required|string|max:190',
            'website'=>'nullable|url|max:190',
            'logo'=>'nullable|image|max:2048',
        ]);

        $data = [
            'name'=>$request->name,
            'website'=>$request->website,
            'updated_at'=>now(),
        ];

        if ($request->hasFile('logo')) {
            @mkdir(public_path('uploads/partners'), 0775, true);
            $logo = 'uploads/partners/'.Str::random(8).'_'.time().'.'.$request->file('logo')->getClientOriginalExtension();
            $request->file('logo')->move(public_path('uploads/partners'), basename($logo));
            $data['logo'] = $logo;
        }

        DB::table('partners')->where('id',$id)->update($data);
        return redirect()->route('admin.partners.index')->with('status', __('Partner updated'));
    }

    public function destroy($id)
    {
        DB::table('partners')->where('id',$id)->delete();
        return redirect()->route('admin.partners.index')->with('status', __('Partner deleted'));
    }
}
