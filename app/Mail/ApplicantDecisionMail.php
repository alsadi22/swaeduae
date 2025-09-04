<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicantDecisionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $decision,                 // approved|waitlist|declined
        public string $opportunityTitle,
        public string $orgName,
        public ?string $note = null,
        public ?string $brandColor = null,
        public ?string $logo = null
    ) {}

    public function build()
    {
        $subjectMap = [
            'approved' => __('Your application is approved'),
            'waitlist' => __('You are on the waitlist'),
            'declined' => __('Your application status'),
        ];

        return $this->subject($subjectMap[$this->decision] ?? __('Application update'))
            ->view('emails.applicant_decision')
            ->with([
                'decision' => $this->decision,
                'opportunityTitle' => $this->opportunityTitle,
                'orgName' => $this->orgName,
                'note' => $this->note,
                'brandColor' => $this->brandColor ?: '#0d6efd',
                'logo' => $this->logo,
            ]);
    }
}
