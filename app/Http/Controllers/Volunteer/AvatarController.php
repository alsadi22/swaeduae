<?php

namespace App\Http\Controllers\Volunteer;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AvatarController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'avatar' => ['required','image','mimes:jpeg,jpg,png,webp','max:2048'],
        ]);

        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $ext  = $request->file('avatar')->getClientOriginalExtension();
        $name = $user->id.'-'.Str::random(8).'.'.$ext;
        $path = $request->file('avatar')->storeAs('avatars', $name, 'public');

        $user->forceFill(['avatar_path' => $path])->save();

        return back()->with('status', 'Photo updated.');
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->forceFill(['avatar_path' => null])->save();

        return back()->with('status', 'Photo removed.');
    }
}
