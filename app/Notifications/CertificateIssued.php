<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Certificate;

class CertificateIssued extends Notification
{
    use Queueable;

    public $certificate;

    public function __construct(Certificate $certificate)
    {
        $this->certificate = $certificate;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'certificate_id' => $this->certificate->id,
            'title' => $this->certificate->title,
            'date' => $this->certificate->issue_date,
        ];
    }
}
