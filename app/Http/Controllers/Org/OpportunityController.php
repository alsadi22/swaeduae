<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class OpportunityController extends Controller
{
    public function index()
    {
        $opps = Opportunity::latest()->paginate(10);
        return view('org.opportunities.index', compact('opps'));
    }

    public function create()
    {
        return view('org.opportunities.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'summary'     => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'region'      => 'nullable|string|max:255',
            'cause'       => 'nullable|string|max:255',
            'skills'      => 'nullable|string|max:255',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'capacity'    => 'nullable|integer|min:1',
            'location'    => 'nullable|string|max:255',
        ]);

        $data['slug'] = Str::slug($data['title']).'-'.Str::random(6);

        if (Schema::hasColumn('opportunities','is_published')) {
            $data['is_published'] = false;
        } elseif (Schema::hasColumn('opportunities','status')) {
            $data['status'] = 'pending';
        }

        Opportunity::create($data);

        return redirect()->route('org.opportunities.index')
            ->with('success', 'Opportunity submitted for review.');
    }
}
