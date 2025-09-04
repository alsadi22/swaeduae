<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q', ''));
        $users = User::query()
            ->when($q !== '', function ($qry) use ($q) {
                $qry->where(function ($s) use ($q) {
                    $s->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create() {}
    public function store(Request $r) {}
    public function show(User $user) {}
    public function edit(User $user) {}
    public function update(Request $r, User $user) {}
    public function destroy(User $user) {}
}
