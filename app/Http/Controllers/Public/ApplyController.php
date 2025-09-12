<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Opportunity;
use App\Models\Application;

class ApplyController extends Controller
{
    public function store(Request $request, string $slug)
    {
        $user = $request->user();
        abort_unless($user, 403);

        $opportunity = Opportunity::where('slug', $slug)->firstOrFail();

        $application = Application::firstOrCreate(
            ['user_id' => $user->id, 'opportunity_id' => $opportunity->id],
            ['status'  => 'submitted']
        );

        return redirect('/account/applications')->with('status', 'Application submitted.');
    }
}
