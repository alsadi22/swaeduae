<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicantsController extends Controller
{
    public function index($eventId)
    {
        // In routes it's {event}, but this is actually an opportunity id.
        $opportunity = DB::table('opportunities')->where('id', $eventId)->first();
        if (!$opportunity) {
            abort(404, 'Opportunity not found');
        }

        // Prefer applications→users join. Fall back to event_registrations if available.
        if (Schema::hasTable('event_registrations')) {
            $rows = DB::table('event_registrations')
                ->where('opportunity_id', $opportunity->id)
                ->get();
        } else {
            $rows = DB::table('applications AS a')
                ->join('users AS u', 'u.id', '=', 'a.user_id')
                ->where('a.opportunity_id', $opportunity->id)
                ->select([
                    'a.id as application_id',
                    'a.status',
                    'a.created_at as applied_at',
                    'u.id as user_id',
                    'u.name as volunteer_name',
                    'u.email as volunteer_email',
                ])
                ->orderBy('a.created_at', 'desc')
                ->get();
        }

        return view('org.events.volunteers', [
            'rows' => $rows,
            'opportunityId' => $opportunity->id,
            'opportunity'   => $opportunity,
        ]);
    }

    // Route: org.applicants.export › Org\ApplicantsController@exportCsv
    public function exportCsv($eventId)
    {
        $opportunity = DB::table('opportunities')->where('id', $eventId)->first();
        if (!$opportunity) {
            abort(404, 'Opportunity not found');
        }

        $rows = DB::table('applications AS a')
            ->join('users AS u', 'u.id', '=', 'a.user_id')
            ->where('a.opportunity_id', $opportunity->id)
            ->select([
                'a.id as application_id',
                'a.status',
                'a.created_at as applied_at',
                'u.id as user_id',
                'u.name as volunteer_name',
                'u.email as volunteer_email',
            ])
            ->orderBy('a.created_at', 'desc')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="applicants-'.$opportunity->id.'.csv"',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['application_id','status','applied_at','user_id','volunteer_name','volunteer_email']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->application_id,
                    $r->status,
                    $r->applied_at,
                    $r->user_id,
                    $r->volunteer_name,
                    $r->volunteer_email,
                ]);
            }
            fclose($out);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
