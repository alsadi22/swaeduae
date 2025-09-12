<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class OpportunityController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q',''));
        $exists = Schema::hasTable('opportunities');
        $rows = $exists ? DB::table('opportunities')
            ->when($q !== '', fn($qry)=>$qry->where(fn($w)=>$w
                ->where('title','like',"%{$q}%")
                ->orWhere('slug','like',"%{$q}%")
                ->orWhere('location','like',"%{$q}%")))
            ->orderByDesc('id')->paginate(15)->withQueryString() : null;

        return view('admin.opportunities.index', compact('rows','exists','q'));
    }

    public function create() { return view('admin.opportunities.create'); }

    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required|max:190',
            'location'=>'nullable|max:190',
            'start_date'=>'nullable|date',
            'end_date'=>'nullable|date|after_or_equal:start_date',
            'description'=>'nullable|string',
            'seats'=>'nullable|integer|min:1',
        ]);
        $slug = $request->slug ?: Str::slug($request->title.'-'.Str::random(4));
        DB::table('opportunities')->insert([
            'title'=>$request->title, 'slug'=>$slug, 'location'=>$request->location,
            'start_date'=>$request->start_date, 'end_date'=>$request->end_date,
            'description'=>$request->description, 'seats'=>$request->seats,
            'created_at'=>now(), 'updated_at'=>now(),
        ]);
        return redirect()->route('admin.opportunities.index')->with('status', __('Opportunity created'));
    }

    public function edit($id)
    {
        $item = DB::table('opportunities')->where('id',$id)->first();
        abort_unless($item, 404);
        return view('admin.opportunities.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'=>'required|max:190',
            'location'=>'nullable|max:190',
            'start_date'=>'nullable|date',
            'end_date'=>'nullable|date|after_or_equal:start_date',
            'description'=>'nullable|string',
            'seats'=>'nullable|integer|min:1',
        ]);

        $data = [
            'title'=>$request->title,
            'location'=>$request->location,
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date,
            'description'=>$request->description,
            'seats'=>$request->seats,
            'updated_at'=>now(),
        ];
        if ($request->filled('slug')) $data['slug'] = $request->slug;

        DB::table('opportunities')->where('id',$id)->update($data);
        return redirect()->route('admin.opportunities.index')->with('status', __('Opportunity updated'));
    }

    public function destroy($id)
    {
        DB::table('opportunities')->where('id',$id)->delete();
        return redirect()->route('admin.opportunities.index')->with('status', __('Opportunity deleted'));
    }
}
