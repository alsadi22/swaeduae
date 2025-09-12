<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        abort_unless($user, 403);
        return view('volunteer.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $rules = [
            'name'        => ['required','string','max:255'],
            'email'       => ['required','email','max:255'],
            'phone'       => ['nullable','string','max:30'],
            'nationality' => ['nullable','string','max:80'],
            'gender'      => ['nullable','in:Male,Female,Other'],
            'dob'         => ['nullable','date'],
            'emirate'     => ['nullable','string','max:80'],
            'city'        => ['nullable','string','max:120'],
            'passport_no' => ['nullable','string','max:80'],
            'emirates_id' => ['nullable','string','max:80'],
            'education'   => ['nullable','string','max:255'],
            'experience'  => ['nullable','string','max:255'],
            'languages'   => ['nullable','string','max:255'],
            'skills'      => ['nullable','string','max:255'],
            'interests'   => ['nullable','string','max:255'],
            'availability'=> ['nullable','string','max:255'],
            'bio'         => ['nullable','string','max:5000'],
            'photo'       => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
            'tos'         => ['sometimes','accepted'],
        ];

        $validated = $request->validate($rules);

        // Assign simple fields if column exists (schema-safe)
        foreach ($validated as $key => $val) {
            if (in_array($key, ['photo','tos'])) continue;
            if (Schema::hasColumn('users', $key)) {
                $user->{$key} = $val;
            }
        }

        // Terms accept
        if ($request->filled('tos') && Schema::hasColumn('users','tos_accepted_at')) {
            $user->tos_accepted_at = now();
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $folder = public_path('uploads/profiles/'.$user->id);
            if (!File::exists($folder)) File::makeDirectory($folder, 0775, true);

            // delete old if exists and different file
            if (!empty($user->photo_path)) {
                $old = public_path(ltrim($user->photo_path,'/'));
                if (File::exists($old)) @File::delete($old);
            }

            $ext = strtolower($request->file('photo')->getClientOriginalExtension());
            $fname = 'photo_'.time().'_'.Str::random(6).'.'.$ext;
            $request->file('photo')->move($folder, $fname);
            $user->photo_path = '/uploads/profiles/'.$user->id.'/'.$fname;
        }

        // Don’t allow email change to break login; you can remove this if allowed
        $user->name  = $validated['name'];
        $user->email = $validated['email'];

        $user->save();

        return redirect()->route('volunteer.profile')->with('status','profile-updated');
    }
}
