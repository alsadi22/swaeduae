<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrganizationDashboardController extends Controller
{
    public function index()
    {
        // Dummy data
        $attendance = [
            ['name' => 'John Doe', 'event' => 'Beach Cleanup', 'hours' => 5, 'status' => 'Present'],
            ['name' => 'Jane Smith', 'event' => 'Tree Planting', 'hours' => 3, 'status' => 'No-show'],
        ];
        return view('organization.dashboard', compact('attendance'));
    }

    public function exportCsv()
    {
        $data = "Name,Event,Hours,Status\nJohn Doe,Beach Cleanup,5,Present\nJane Smith,Tree Planting,3,No-show";
        return response($data)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="attendance.csv"');
    }
}
