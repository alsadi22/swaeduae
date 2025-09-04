<?php

namespace App\Http\Controllers\My;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VolunteerProfile;

class ProfileController extends Controller
{
    public function show()
    {
        return view('my.profile');
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name_ar'              => 'nullable|string|max:160',
            'gender'               => 'nullable|string|max:20',
            'dob'                  => 'nullable|date',
            'nationality'          => 'nullable|string|max:80',
            'emirate'              => 'nullable|string|max:80',
            'city'                 => 'nullable|string|max:120',
            'emirates_id'          => 'nullable|string|max:50',
            'emirates_id_expiry'   => 'nullable|date',
            'mobile'               => 'nullable|string|max:60',
            'phone'                => 'nullable|string|max:60',
        ]);

        // Update User contact fields if provided
        if ($request->filled('mobile')) $user->mobile = $request->input('mobile');
        if ($request->filled('phone'))  $user->phone  = $request->input('phone');
        $user->save();

        // Upsert Volunteer profile
        $vp = VolunteerProfile::firstOrNew(['user_id' => $user->id]);
        $vp->fill($request->only([
            'name_ar','gender','dob','nationality','emirate','city',
            'emirates_id','emirates_id_expiry'
        ]));
        $vp->save();

        return back()->with('status', 'Profile updated');
    }
}
