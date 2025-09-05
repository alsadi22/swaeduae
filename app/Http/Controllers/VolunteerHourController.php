<?php

namespace App\Http\Controllers;

use App\Models\VolunteerHour;
use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VolunteerHourController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');
        $hours = VolunteerHour::with('opportunity')
            ->where('user_id',$user->id)->latest('date')->paginate(15);
        $opps = Opportunity::orderBy('title')->pluck('title','id');
        return view('hours.index', compact('hours','opps'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $data = $request->validate([
            'opportunity_id' => 'nullable|exists:opportunities,id',
            'hours' => 'required|numeric|min:0.25|max:24',
            'date'  => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);
        $data['user_id'] = $user->id;
        $data['status']  = 'pending';

        VolunteerHour::create($data);
        return back()->with('success','Hours submitted for verification.');
    }

    // Admin verify/reject
    public function setStatus(Request $request, VolunteerHour $hour)
    {
        $request->validate(['status'=>'required|in:pending,verified,rejected']);
        $hour->status = $request->status;
        $hour->verified_by = $request->user()->id;
        $hour->save();
        return back()->with('success','Hour status updated.');
    }
}
