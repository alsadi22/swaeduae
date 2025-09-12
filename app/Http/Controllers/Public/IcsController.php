<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Opportunity;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class IcsController extends Controller
{
    public function show(string $slug)
    {
        // Try Eloquent w/o global scopes first
        $elo = null;
        try {
            $elo = Opportunity::query()->withoutGlobalScopes()->where('slug', $slug)->first();
        } catch (\Throwable $e) {
            // If model not bound or has issues, we fallback below
        }

        if ($elo) {
            $o = (object)[
                'id'        => $elo->id,
                'slug'      => $elo->slug,
                'title'     => $elo->title,
                'location'  => $elo->location,
                'starts_at' => $elo->starts_at,
                'ends_at'   => $elo->ends_at,
            ];
        } else {
            // Raw DB fallback (bypasses model scopes entirely)
            $row = DB::table('opportunities')->where('slug', $slug)->first();
            if (!$row) abort(404);
            $o = (object)$row;
        }

        $start = !empty($o->starts_at) ? Carbon::parse($o->starts_at)->utc()->format('Ymd\THis\Z') : null;
        $end   = !empty($o->ends_at)
            ? Carbon::parse($o->ends_at)->utc()->format('Ymd\THis\Z')
            : ($start ? Carbon::parse($o->starts_at)->addHours(2)->utc()->format('Ymd\THis\Z') : null);

        $title = str_replace(["\r","\n"], ' ', (string)($o->title ?? 'Opportunity'));
        $loc   = str_replace(["\r","\n"], ' ', (string)($o->location ?? ''));
        $url   = url('/opportunities/'.$o->slug);
        $uid   = (($o->id ?? Str::uuid()).'@swaeduae.ae');

        $ics  = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//SwaedUAE//EN\r\nBEGIN:VEVENT\r\n";
        $ics .= "UID:$uid\r\nDTSTAMP:".gmdate('Ymd\THis\Z')."\r\n";
        if ($start) $ics .= "DTSTART:$start\r\n";
        if ($end)   $ics .= "DTEND:$end\r\n";
        $ics .= "SUMMARY:$title\r\n";
        if ($loc)  $ics .= "LOCATION:$loc\r\n";
        $ics .= "URL:$url\r\nEND:VEVENT\r\nEND:VCALENDAR\r\n";

        return new Response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.(($o->slug ?: 'event')).'.ics"',
        ]);
    }
}
