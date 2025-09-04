<?php

namespace App\Mail;

use App\Models\VolunteerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VolunteerApplicationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $application;

    public function __construct(VolunteerApplication $application)
    {
        $this->application = $application;
    }

    public function build()
    {
        return $this->subject(__('messages.application_email_subject'))
                    ->view('emails.volunteer.application')
                    ->with(['application' => $this->application]);
    }
}
