<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.users', compact('users'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit_user', compact('user'));
    }

    public function update(Request $request, $id)
    {
        // Update logic here
        return redirect()->route('admin.users.index');
    }

    // Add this function:
    public function toggle($id)
    {
        $user = User::findOrFail($id);
        $user->enabled = !$user->enabled;
        $user->save();

        return redirect()->route('admin.users.index');
    }
}
