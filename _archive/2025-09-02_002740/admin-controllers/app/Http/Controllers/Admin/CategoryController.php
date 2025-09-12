<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    protected function table()
    {
        if (Schema::hasTable('categories')) return 'categories';
        if (Schema::hasTable('opportunity_categories')) return 'opportunity_categories';
        return null;
    }

    public function index(Request $request)
    {
        $q = trim((string)$request->get('q',''));
        $table = $this->table();
        $exists = $table !== null;
        $rows = $exists ? DB::table($table)
            ->when($q !== '', fn($qry)=>$qry->where(fn($w)=>$w
                ->where('name','like',"%{$q}%")
                ->orWhere('title','like',"%{$q}%")
                ->orWhere('slug','like',"%{$q}%")))
            ->orderByDesc('id')->paginate(15)->withQueryString() : null;

        return view('admin.categories.index', compact('rows','exists','q','table'));
    }

    public function create()
    {
        $table = $this->table(); abort_unless($table, 404);
        return view('admin.categories.create', compact('table'));
    }

    public function store(Request $request)
    {
        $table = $this->table(); abort_unless($table, 404);
        $request->validate(['name'=>'required|max:190','slug'=>'nullable|max:190']);
        DB::table($table)->insert([
            'name'=>$request->name,
            'slug'=>$request->slug ?: Str::slug($request->name),
            'created_at'=>now(), 'updated_at'=>now(),
        ]);
        return redirect()->route('admin.categories.index')->with('status', __('Category created'));
    }

    public function edit($id)
    {
        $table = $this->table(); abort_unless($table, 404);
        $item = DB::table($table)->where('id',$id)->first(); abort_unless($item, 404);
        return view('admin.categories.edit', compact('item','table'));
    }

    public function update(Request $request, $id)
    {
        $table = $this->table(); abort_unless($table, 404);
        $request->validate(['name'=>'required|max:190','slug'=>'nullable|max:190']);
        DB::table($table)->where('id',$id)->update([
            'name'=>$request->name,
            'slug'=>$request->slug ?: Str::slug($request->name),
            'updated_at'=>now(),
        ]);
        return redirect()->route('admin.categories.index')->with('status', __('Category updated'));
    }

    public function destroy($id)
    {
        $table = $this->table(); abort_unless($table, 404);
        DB::table($table)->where('id',$id)->delete();
        return redirect()->route('admin.categories.index')->with('status', __('Category deleted'));
    }
}
