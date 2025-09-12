<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Opportunity;

class OpportunityController extends Controller
{
    // List all opportunities
    public function index()
    {
        $opps = Opportunity::latest()->paginate(10);
        return view('volunteer.opportunities', compact('opps'));
    }

    // Show single opportunity detail
    public function show($id)
    {
        $opp = Opportunity::findOrFail($id);
        return view('volunteer.opportunity_detail', compact('opp'));
    }
}
