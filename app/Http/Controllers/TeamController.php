<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;

class TeamController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $team = $user->team ?? null;

        $opportunities = $team ? $team->opportunities()->paginate(10) : collect();

        return view('dashboard.team', compact('team','opportunities'));
    }
}
