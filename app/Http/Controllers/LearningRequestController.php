<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LearningRequestController extends Controller
{
    public function create()
    {
        return view('learning_requests.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required|string|max:190',
            'details'=>'nullable|string|max:5000',
        ]);

        DB::table('learning_requests')->insert([
            'user_id'=>$request->user()->id,
            'title'=>$request->title,
            'details'=>$request->details,
            'status'=>'pending',
            'created_at'=>now(), 'updated_at'=>now(),
        ]);

        return redirect()->route('learning.create')->with('status', __('Request submitted'));
    }
}
