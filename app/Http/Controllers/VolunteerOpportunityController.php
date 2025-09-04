<?php

namespace App\Http\Controllers;

use App\Models\VolunteerOpportunity;

class VolunteerOpportunityController extends Controller
{
    public function index()
    {
        $opportunities = VolunteerOpportunity::where('status', 'active')
            ->orderBy('date', 'asc')
            ->paginate(9);

        return view('volunteer.opportunities', compact('opportunities'));
    }

    public function show($id)
    {
        $opportunity = VolunteerOpportunity::findOrFail($id);

        return view('volunteer.opportunity_show', compact('opportunity'));
    }
}
