<?php

namespace App\Mail;

use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Opportunity $opportunity, public User $user) {}

    public function build()
    {
        $opp = $this->opportunity;
        $uid = 'swaed-'.($opp->id).'-'.md5($opp->title.$opp->starts_at.$opp->ends_at);

        $starts = optional($opp->starts_at)->clone()?->timezone('UTC');
        $ends   = optional($opp->ends_at)->clone()?->timezone('UTC');

        $dtStart = $starts ? $starts->format('Ymd\THis\Z') : now('UTC')->format('Ymd\THis\Z');
        $dtEnd   = $ends   ? $ends->format('Ymd\THis\Z')   : now('UTC')->addHour()->format('Ymd\THis\Z');

        $summary = $opp->title ?: 'Volunteer Opportunity';
        $desc    = trim(($opp->description ?? ''));
        $loc     = trim(($opp->location ?: $opp->city ?: 'UAE'));

        $ics = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//SawaedUAE//EN\r\nMETHOD:PUBLISH\r\nBEGIN:VEVENT\r\nUID:$uid\r\nDTSTAMP:".now('UTC')->format('Ymd\THis\Z')."\r\nDTSTART:$dtStart\r\nDTEND:$dtEnd\r\nSUMMARY:".addcslashes($summary,"\\,;")."\r\nLOCATION:".addcslashes($loc,"\\,;")."\r\nDESCRIPTION:".addcslashes($desc,"\\,;")."\r\nEND:VEVENT\r\nEND:VCALENDAR\r\n";

        return $this->subject(__('Application received: ').$summary)
            ->view('emails.application_submitted')
            ->with(['opportunity'=>$opp,'user'=>$this->user])
            ->attachData($ics, 'event.ics', ['mime' => 'text/calendar; charset=UTF-8']);
    }
}
