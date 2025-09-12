<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class VolunteerLeftAreaOrgMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Event $event, public Carbon $lastSeen)
    {
    }

    public function build()
    {
        $view = 'mail.volunteer_left_area_org_' . app()->getLocale();
        if (!view()->exists($view)) {
            $view = 'mail.volunteer_left_area_org_en';
        }
        return $this->subject(__('Volunteer left event area'))
            ->markdown($view, [
                'event' => $this->event,
                'lastSeen' => $this->lastSeen,
            ]);
    }
}
