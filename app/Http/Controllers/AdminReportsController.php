<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminReportsController extends Controller
{
    public function index()
    {
        // Hours per month
        $hoursByMonth = Attendance::select(
            DB::raw('MONTH(checked_in_at) as month'),
            DB::raw('SUM(hours) as total')
        )->groupBy('month')->orderBy('month')->pluck('total', 'month');

        // Most active volunteers
        $topVolunteers = Attendance::select('user_id', DB::raw('SUM(hours) as total_hours'))
            ->groupBy('user_id')->orderByDesc('total_hours')->take(5)->with('user')->get();

        // Top organizations
        $topOrgs = Event::select('created_by', DB::raw('COUNT(*) as total_events'))
            ->groupBy('created_by')->orderByDesc('total_events')->take(5)->with('creator')->get();

        return view('admin.reports', compact('hoursByMonth', 'topVolunteers', 'topOrgs'));
    }

    public function export($type)
    {
        $data = [
            'hoursByMonth' => Attendance::select(
                DB::raw('MONTH(checked_in_at) as month'),
                DB::raw('SUM(hours) as total')
            )->groupBy('month')->orderBy('month')->pluck('total', 'month')->toArray(),

            'topVolunteers' => Attendance::select('user_id', DB::raw('SUM(hours) as total_hours'))
                ->groupBy('user_id')->orderByDesc('total_hours')->take(5)->with('user')->get()->toArray(),

            'topOrgs' => Event::select('created_by', DB::raw('COUNT(*) as total_events'))
                ->groupBy('created_by')->orderByDesc('total_events')->take(5)->with('creator')->get()->toArray(),
        ];

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('admin.reports_export', $data);
            return $pdf->download('reports.pdf');
        } elseif ($type === 'csv') {
            $filename = "reports.csv";
            $handle = fopen($filename, 'w+');
            fputcsv($handle, ['Report Section', 'Label', 'Value']);

            foreach ($data['hoursByMonth'] as $month => $total) {
                fputcsv($handle, ['Hours by Month', $month, $total]);
            }
            foreach ($data['topVolunteers'] as $vol) {
                fputcsv($handle, ['Top Volunteers', $vol['user']['name'] ?? '', $vol['total_hours']]);
            }
            foreach ($data['topOrgs'] as $org) {
                fputcsv($handle, ['Top Organizations', $org['creator']['name'] ?? '', $org['total_events']]);
            }
            fclose($handle);

            return response()->download($filename)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Invalid export type');
    }
}
