<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BadgeController extends Controller
{
    public function index()
    {
        $badges = [
            ['title' => 'First Volunteer Hour', 'icon' => '🏅'],
            ['title' => '5 Events Completed', 'icon' => '🥇'],
            ['title' => '50 Hours Served', 'icon' => '🎖️'],
        ];

        return view('badges.index', ['badges' => $badges]);
    }
}
