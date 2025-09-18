<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\RedirectResponse;

class ApplicationReviewController extends Controller
{
    public function shortlist(Application $application): RedirectResponse
    {
        $application->update(['status' => 'shortlisted']);
        return back()->with('status', 'Shortlisted');
    }

    public function accept(Application $application): RedirectResponse
    {
        $application->update(['status' => 'accepted']);
        return back()->with('status', 'Accepted');
    }

    public function reject(Application $application): RedirectResponse
    {
        $application->update(['status' => 'rejected']);
        return back()->with('status', 'Rejected');
    }
}
