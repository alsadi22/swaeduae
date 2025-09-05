<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Opportunity;
use App\Models\Category;

class OrganizationOpportunityController extends Controller
{
    public function index(Request $request)
    {
        $organization = $request->user()->organization;
        $opportunities = $organization->opportunities()->paginate(10);
        return view('organization_opportunities.index', compact('opportunities'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('organization_opportunities.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $organization = $request->user()->organization;
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'location'    => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'is_virtual'  => 'boolean',
            'volunteers_required' => 'nullable|integer|min:1',
        ]);

        $organization->opportunities()->create($data);

        return redirect()->route('organization.opportunities.index')
            ->with('status','Opportunity created successfully.');
    }

    public function edit(Opportunity $opportunity)
    {
        $this->authorize('update', $opportunity);
        $categories = Category::all();
        return view('organization_opportunities.edit', compact('opportunity','categories'));
    }

    public function update(Request $request, Opportunity $opportunity)
    {
        $this->authorize('update', $opportunity);

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'location'    => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'is_virtual'  => 'boolean',
            'volunteers_required' => 'nullable|integer|min:1',
        ]);

        $opportunity->update($data);

        return redirect()->route('organization.opportunities.index')
            ->with('status','Opportunity updated successfully.');
    }

    public function destroy(Opportunity $opportunity)
    {
        $this->authorize('delete', $opportunity);
        $opportunity->delete();
        return back()->with('status','Opportunity deleted.');
    }
}
