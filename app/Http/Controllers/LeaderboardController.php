<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function volunteers()
    {
        $volunteers = [
            ['name' => 'John Doe', 'hours' => 120],
            ['name' => 'Jane Smith', 'hours' => 95],
            ['name' => 'Ali Hassan', 'hours' => 80],
        ];
        return view('leaderboard.volunteers', compact('volunteers'));
    }

    public function organizations()
    {
        $organizations = [
            ['name' => 'Green Earth Org', 'hours' => 500],
            ['name' => 'Food Aid UAE', 'hours' => 420],
            ['name' => 'Youth for Change', 'hours' => 350],
        ];
        return view('leaderboard.organizations', compact('organizations'));
    }
}
