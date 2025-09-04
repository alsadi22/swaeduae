<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
        $this->subject('[Contact] '.$payload['subject']);
    }

    public function build()
    {
        // Force from to your domain (Zoho requirement), reply-to user
        $fromAddr = config('mail.from.address');
        $fromName = config('mail.from.name');

        return $this->from($fromAddr, $fromName)
                    ->replyTo($this->payload['email'], $this->payload['name'])
                    ->markdown('emails.contact.message', ['payload' => $this->payload]);
    }
}
