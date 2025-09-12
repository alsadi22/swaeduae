<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Setting;

class AdminPanelController extends Controller
{
    public function dashboard()
    {
        $userCount = User::count();
        // Add more dashboard stats as needed
        return view('admin.dashboard', compact('userCount'));
    }

    public function settings()
    {
        $settings = Setting::first();
        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $settings = Setting::first();
        $settings->site_name = $request->input('site_name');
        $settings->save();
        return redirect()->route('admin.settings')->with('success', 'Settings updated!');
    }
}
