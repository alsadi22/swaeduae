<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSettingsController extends Controller
{
    public function index()
    {
        // Assuming settings are stored in config or DB, load them here
        // For demo, just show the settings view
        return view('admin.settings');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_email' => 'required|email',
            'logo' => 'nullable|image|max:2048',
            'about' => 'nullable|string',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'instagram' => 'nullable|url',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('public/logo');
            // Save $path in your settings DB or config
        }

        // Save other settings to DB or config as needed

        return redirect()->route('admin.settings')->with('success', 'Settings updated.');
    }
}
