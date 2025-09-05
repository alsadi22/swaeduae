<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;

    public function __construct(array $data){ $this->data = $data; }

    public function build()
    {
        $subject = $this->data['subject'] ?? 'Website contact';
        return $this->subject($subject)
            ->markdown('mails.contact_message')
            ->with(['data' => $this->data]);
    }
}
