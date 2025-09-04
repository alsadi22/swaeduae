<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /** return first existing date column from the list */
    private function firstCol(string $table, array $cands) {
        foreach ($cands as $c) if (Schema::hasColumn($table,$c)) return $c;
        return null;
    }

    private function pickRangeCols(string $table): array {
        $start = $this->firstCol($table, ['start_date','event_date','start_at','start_time','date','created_at']);
        $end   = $this->firstCol($table, ['end_date','end_at','finish_date']);
        return [$start, $end];
    }

    private function allUpcomingFrom(string $table, string $titleCol = 'title') {
        if (!Schema::hasTable($table)) return collect();
        [$startCol, $endCol] = $this->pickRangeCols($table);
        if (!$startCol) return collect();

        $q = DB::table($table.' as t')->select('t.*');
        $q->whereNotNull($startCol);
        $q->orderBy($startCol,'asc')->orderBy('id','desc');

        // only next 6 months
        $from = Carbon::now()->startOfDay();
        $to   = Carbon::now()->addMonths(6)->endOfDay();
        $q->whereBetween(DB::raw("DATE(t.`$startCol`)"), [$from->toDateString(), $to->toDateString()]);

        $rows = $q->limit(500)->get();
        // normalize
        return $rows->map(function($r) use ($table,$titleCol,$startCol,$endCol) {
            $title = $r->$titleCol ?? ucfirst(rtrim($table,'s'));
            $start = Carbon::parse($r->$startCol ?? $r->created_at);
            $end   = $endCol && !empty($r->$endCol) ? Carbon::parse($r->$endCol) : null;
            if (!$end) {
                // all-day; DTEND should be exclusive
                $end = (clone $start)->addDay();
            }
            return (object)[
                'id'    => $r->id,
                'title' => $title,
                'start' => $start,
                'end'   => $end,
                'table' => $table,
                'region'=> $r->region ?? null,
                'slug'  => \Illuminate\Support\Str::slug($title),
            ];
        });
    }

    public function index(Request $request) {
        $month = (int)($request->get('m', now()->month));
        $year  = (int)($request->get('y', now()->year));
        $first = Carbon::create($year,$month,1)->startOfMonth();
        $last  = (clone $first)->endOfMonth();

        // pull from both opportunities & events if present
        $opps   = $this->allUpcomingFrom('opportunities');
        $events = $this->allUpcomingFrom('events','name'); // many schemas use 'name'
        $all = $opps->merge($events);

        // only events in this month for display
        $inMonth = $all->filter(fn($e)=>$e->start->between($first,$last));

        return view('events/calendar', [
            'month'=>$first, 'prev'=>$first->copy()->subMonth(), 'next'=>$first->copy()->addMonth(),
            'items'=>$inMonth,
        ]);
    }

    /** Build a single VEVENT string */
    private function vevent($uid, $summary, Carbon $start, Carbon $end, $url = null, $desc = null, $loc = null): string {
        $fmtDate = fn(Carbon $c)=>$c->format('Ymd');
        $lines = [
            'BEGIN:VEVENT',
            'UID:'.$uid,
            'SUMMARY:'.$this->icsEscape($summary),
            'DTSTART;VALUE=DATE:'.$fmtDate($start),
            'DTEND;VALUE=DATE:'.$fmtDate($end),
        ];
        if ($url) $lines[] = 'URL:'.$this->icsEscape($url);
        if ($desc) $lines[] = 'DESCRIPTION:'.$this->icsEscape($desc);
        if ($loc) $lines[] = 'LOCATION:'.$this->icsEscape($loc);
        $lines[] = 'END:VEVENT';
        return implode("\r\n", $lines);
    }

    private function icsResponse(array $events, string $name = 'calendar.ics') {
        $ics = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//SawaedUAE//Calendar//EN\r\nCALSCALE:GREGORIAN\r\n";
        $ics.= implode("\r\n", $events)."\r\nEND:VCALENDAR\r\n";
        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$name.'"',
        ]);
    }

    private function icsEscape(string $s): string {
        $s = str_replace(["\\", ";", ",", "\n", "\r"], ["\\\\","\\;","\\,","\\n",""], $s);
        return $s;
    }

    /** All upcoming (next 6 months) */
    public function icsAll() {
        $events = [];

        foreach ($this->allUpcomingFrom('opportunities') as $e) {
            $events[] = $this->vevent(
                'opp-'.$e->id.'@swaeduae',
                $e->title,
                $e->start,
                $e->end,
                url('/opportunities/'.$e->id),
                $e->region ? ('Region: '.$e->region) : null
            );
        }
        foreach ($this->allUpcomingFrom('events','name') as $e) {
            $events[] = $this->vevent(
                'evt-'.$e->id.'@swaeduae',
                $e->title,
                $e->start,
                $e->end,
                url('/events/'.$e->id),
                $e->region ? ('Region: '.$e->region) : null
            );
        }

        return $this->icsResponse($events, 'sawaeduae_calendar.ics');
    }

    /** Single opportunity */
    public function icsOpp($id) {
        if (!Schema::hasTable('opportunities')) abort(404);
        [$startCol, $endCol] = $this->pickRangeCols('opportunities');
        $o = DB::table('opportunities')->where('id',$id)->first();
        abort_unless($o, 404);
        $start = Carbon::parse($o->$startCol ?? $o->created_at);
        $end   = $endCol && !empty($o->$endCol) ? Carbon::parse($o->$endCol) : (clone $start)->addDay();
        $title = $o->title ?? 'Opportunity #'.$id;

        $v = $this->vevent('opp-'.$id.'@swaeduae', $title, $start, $end, url('/opportunities/'.$id), $o->region ?? null);
        return $this->icsResponse([$v], 'opportunity_'.$id.'.ics');
    }

    /** Single event (events table) */
    public function icsEvent($id) {
        if (!Schema::hasTable('events')) abort(404);
        [$startCol, $endCol] = $this->pickRangeCols('events');
        $o = DB::table('events')->where('id',$id)->first();
        abort_unless($o, 404);
        $start = Carbon::parse($o->$startCol ?? $o->created_at);
        $end   = $endCol && !empty($o->$endCol) ? Carbon::parse($o->$endCol) : (clone $start)->addDay();
        $title = $o->name ?? ($o->title ?? 'Event #'.$id);

        $v = $this->vevent('evt-'.$id.'@swaeduae', $title, $start, $end, url('/events/'.$id), $o->region ?? null);
        return $this->icsResponse([$v], 'event_'.$id.'.ics');
    }
}
