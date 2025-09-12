<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SimpleMessage extends Notification
{
    use Queueable;

    public array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data + [
            'title' => 'Notification',
            'body'  => '',
        ];
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return $this->data;
    }
}
