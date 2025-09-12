<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Opportunity;

class OpportunityAdminController extends Controller
{
    public function index() {
        $items = Opportunity::latest()->paginate(20);
        return view('admin.opportunities.index', compact('items'));
    }
    public function create() {
        return view('admin.opportunities.create');
    }
    public function store(Request $r) {
        $data = $r->validate([
            'title'=>'required|string|max:255',
            'description'=>'required|string',
            'region'=>'nullable|string|max:120',
            'location'=>'nullable|string|max:120',
            'start_date'=>'nullable|date',
            'end_date'=>'nullable|date|after_or_equal:start_date',
            'category'=>'nullable|string|max:120',
        ]);
        $item = Opportunity::create($data);
        return redirect()->route('admin.opportunities.edit',$item->id)->with('ok', __('Created.'));
    }
    public function edit($id) {
        $item = Opportunity::findOrFail($id);
        return view('admin.opportunities.edit', compact('item'));
    }
    public function update(Request $r, $id) {
        $item = Opportunity::findOrFail($id);
        $data = $r->validate([
            'title'=>'required|string|max:255',
            'description'=>'required|string',
            'region'=>'nullable|string|max:120',
            'location'=>'nullable|string|max:120',
            'start_date'=>'nullable|date',
            'end_date'=>'nullable|date|after_or_equal:start_date',
            'category'=>'nullable|string|max:120',
        ]);
        $item->update($data);
        return back()->with('ok', __('Saved.'));
    }
    public function destroy($id) {
        Opportunity::findOrFail($id)->delete();
        return redirect()->route('admin.opportunities.index')->with('ok', __('Deleted.'));
    }
}
