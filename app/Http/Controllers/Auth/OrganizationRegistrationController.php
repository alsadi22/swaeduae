<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Facades\Hash;

class OrganizationRegistrationController extends Controller
{
    public function create()
    {
        return view('organization.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $data['contact_person'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
        ]);

        Organization::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'contact_person' => $data['contact_person'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'],
        ]);

        $user->assignRole('organization');

        return redirect()->route('login')->with('status','Organization registered successfully! Please login.');
    }
}
