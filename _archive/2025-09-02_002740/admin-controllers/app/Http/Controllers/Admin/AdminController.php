<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Example dashboard logic (customize as needed)
        return view('admin.dashboard');
    }

    // --- Users List Method ---
    public function users()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }
}
