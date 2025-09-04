<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Opportunity;

/**
 * Controller responsible for listing and showing volunteer opportunities.
 *
 * This controller replaces the older VolunteerOpportunityController and
 * provides filtering by category, search term and location. It returns
 * paginated collections of opportunities ordered by start date.
 */
class OpportunityController extends Controller
{
    /**
     * Display a listing of active opportunities.
     *
     * @param  Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Opportunity::query();

        // Optional filters
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('location')) {
            $query->where('region', $request->location);
        }

        $opportunities = $query->orderBy('start_date', 'asc')->paginate(12);

        return view('opportunities.index', compact('opportunities'));
    }

    /**
     * Display the specified opportunity.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $opportunity = Opportunity::findOrFail($id);

        return view('opportunities.show', compact('opportunity'));
    }
}