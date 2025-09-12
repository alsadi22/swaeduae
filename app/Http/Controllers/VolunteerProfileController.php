<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VolunteerProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('volunteer.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $user->update($request->only(['name', 'email', 'phone'])); // add validation as needed
        return redirect()->route('volunteer.profile.show')->with('success', 'Profile updated!');
    }
}
