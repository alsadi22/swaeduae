<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EventRegistrationNotification extends Notification
{
    use Queueable;

    protected $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Event Registration Confirmation')
                    ->line('You have successfully registered for the event: ' . $this->event->title)
                    ->action('View Event', route('events.show', ['idOrSlug' => ($this->event->slug ?? $this->event->getKey())]))
                    ->line('Thank you for volunteering!');
    }
}
