<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q',''));
        $exists = Schema::hasTable('events');
        $rows = $exists ? DB::table('events')
            ->when($q !== '', fn($qry)=>$qry->where(fn($w)=>$w
                ->where('title','like',"%{$q}%")
                ->orWhere('name','like',"%{$q}%")
                ->orWhere('location','like',"%{$q}%")))
            ->orderByDesc('id')->paginate(15)->withQueryString() : null;

        return view('admin.events.index', compact('rows','exists','q'));
    }

    public function create() { return view('admin.events.create'); }

    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required|max:190',
            'location'=>'nullable|max:190',
            'date'=>'nullable|date',
            'description'=>'nullable|string',
        ]);

        DB::table('events')->insert([
            'title'=>$request->title,
            'location'=>$request->location,
            'date'=>$request->date,
            'description'=>$request->description,
            'created_at'=>now(), 'updated_at'=>now(),
        ]);

        return redirect()->route('admin.events.index')->with('status', __('Event created'));
    }

    public function edit($id)
    {
        $item = DB::table('events')->where('id',$id)->first();
        abort_unless($item, 404);
        return view('admin.events.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'=>'required|max:190',
            'location'=>'nullable|max:190',
            'date'=>'nullable|date',
            'description'=>'nullable|string',
        ]);
        DB::table('events')->where('id',$id)->update([
            'title'=>$request->title,
            'location'=>$request->location,
            'date'=>$request->date,
            'description'=>$request->description,
            'updated_at'=>now(),
        ]);
        return redirect()->route('admin.events.index')->with('status', __('Event updated'));
    }

    public function destroy($id)
    {
        DB::table('events')->where('id',$id)->delete();
        return redirect()->route('admin.events.index')->with('status', __('Event deleted'));
    }
}
