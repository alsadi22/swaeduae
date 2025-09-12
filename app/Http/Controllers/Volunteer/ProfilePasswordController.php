<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfilePasswordController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required','confirmed','min:8'],
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => __('Current password is incorrect')]);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return back()->with('status', __('Password updated successfully.'));
    }
}
