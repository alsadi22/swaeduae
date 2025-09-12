<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Opportunity;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class OpportunitiesController extends Controller
{
    public function index()
    {
        $opportunities = Opportunity::latest()->paginate(10);
        $user = Auth::user();

        $appliedIds = [];
        if ($user) {
            $appliedIds = Application::where('user_id', $user->id)->pluck('opportunity_id')->toArray();
        }

        return view('opportunities.index', compact('opportunities', 'appliedIds'));
    }

    public function show($id)
    {
        $opportunity = Opportunity::findOrFail($id);
        $user = Auth::user();

        $hasApplied = false;
        if ($user) {
            $hasApplied = Application::where('user_id', $user->id)
                                     ->where('opportunity_id', $id)
                                     ->exists();
        }

        return view('opportunities.show', compact('opportunity', 'hasApplied'));
    }

    public function apply($id)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to apply.');
        }

        $alreadyApplied = Application::where('user_id', $user->id)
                                     ->where('opportunity_id', $id)
                                     ->exists();

        if ($alreadyApplied) {
            return redirect()->back()->with('error', 'You have already applied for this opportunity.');
        }

        Application::create([
            'user_id' => $user->id,
            'opportunity_id' => $id,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Application submitted successfully.');
    }
}
