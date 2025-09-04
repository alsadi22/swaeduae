<?php

namespace App\Mail;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CertificateIssuedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Certificate $cert;

    public function __construct(Certificate $cert)
    {
        $this->cert = $cert->loadMissing(['user','opportunity']);
    }

    public function build()
    {
        $subject = __('Your Volunteer Certificate') . ' / ' . __('شهادة التطوع الخاصة بك');
        return $this->subject($subject)
            ->view('emails.certificate_issued');
    }
}
