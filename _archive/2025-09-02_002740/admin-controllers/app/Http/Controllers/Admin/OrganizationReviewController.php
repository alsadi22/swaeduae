<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationReviewController extends Controller
{
    public function approve(Organization $organization, Request $request)
    {
        $organization->license_status = 'approved';
        $organization->review_notes = $request->input('notes');
        $organization->save();

        return back()->with('status', 'Organization approved.');
    }

    public function reject(Organization $organization, Request $request)
    {
        $request->validate(['notes' => ['required','string','min:5']]);
        $organization->license_status = 'rejected';
        $organization->review_notes = $request->input('notes');
        $organization->save();

        return back()->with('status', 'Organization rejected with notes.');
    }
}
