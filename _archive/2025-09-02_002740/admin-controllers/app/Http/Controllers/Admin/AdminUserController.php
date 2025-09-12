<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
        }

        $users = $query->orderByDesc('created_at')->paginate(15);

        return view('admin.users', compact('users'));
    }

    public function edit(User $user)
    {
        return view('admin.edit_user', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'nullable|string|max:255',
        ]);
        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ]);
        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    public function toggle(User $user)
    {
        $user->enabled = !$user->enabled;
        $user->save();
        return redirect()->route('admin.users.index')->with('success', 'User status updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
