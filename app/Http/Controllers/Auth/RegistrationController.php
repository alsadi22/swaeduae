<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name' => ['required','string','max:255'],
            'email' => ['required','string','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8','confirmed'],
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        $user = new User();
        if (Schema::hasColumn('users', 'name')) $user->name = $request->input('name');
        if (Schema::hasColumn('users', 'email')) $user->email = $request->input('email');
        if (Schema::hasColumn('users', 'password')) $user->password = Hash::make($request->input('password'));

        foreach (['is_admin','is_active'] as $col) {
            if (Schema::hasColumn('users', $col) && !isset($user->{$col})) {
                $user->{$col} = ($col === 'is_active') ? 1 : 0;
            }
        }

        $user->save();

        // Assign volunteer role if package exists
        try { if (method_exists($user, 'assignRole')) { $user->assignRole('volunteer'); } } catch (\Throwable $e) {}

        Auth::login($user);

        // Send verification email (built-in)
        try { $user->sendEmailVerificationNotification(); } catch (\Throwable $e) {}

        // Go to the notice page
        return redirect()->route('verification.notice');
    }
}
