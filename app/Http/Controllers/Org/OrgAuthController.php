<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organization;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

class OrgAuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('org.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'email' => 'required|email|unique:organizations,email',
            'password' => 'required|string|min:8|confirmed',
            'license_number' => 'required|string|max:100',
            'license_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|url',
            'address' => 'nullable|string|max:500',
        ]);

        // Store license file
        $path = $request->file('license_file')->store('uploads/org_licenses', 'public');

        // Create organization
        $org = Organization::create([
            'name_en' => $validated['name_en'],
            'name_ar' => $validated['name_ar'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'license_number' => $validated['license_number'],
            'license_file' => $path,
            'phone' => $validated['phone'] ?? null,
            'website' => $validated['website'] ?? null,
            'address' => $validated['address'] ?? null,
            'license_status' => 'in_review'
        ]);

        event(new Registered($org)); // triggers email verification

        return view('org.under_review', ['organization' => $org]);
    }
}
