<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Opportunity;
use App\Models\Organization;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Simple metrics; expand as needed
        $volunteerCount   = User::role('volunteer')->count();
        $organizationCount = User::role('organization')->count();
        $opportunityCount  = Opportunity::count();

        return view('dashboard.admin', compact('volunteerCount','organizationCount','opportunityCount'));
    }
}
