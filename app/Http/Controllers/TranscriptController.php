<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class TranscriptController extends Controller
{
    public function pdf(Request $request)
    {
        $uid = $request->user()->id;

        $hours = DB::table('volunteer_hours as vh')
            ->leftJoin('opportunities as o','o.id','=','vh.opportunity_id')
            ->where('vh.user_id',$uid)
            ->orderByDesc('vh.id')
            ->select('vh.*','o.id as oid')
            ->get();

        $total = round($hours->sum('minutes')/60, 2);

        $data = [
            'user'  => $request->user(),
            'rows'  => $hours,
            'total' => $total,
            'site'  => DB::table('settings')->where('key','site.name')->value('value') ?? 'SawaedUAE',
        ];

        $pdf = Pdf::loadView('certificates.transcript', $data)->setPaper('A4', 'portrait');
        return $pdf->download("transcript_{$request->user()->volunteer_code}.pdf");
    }
}
