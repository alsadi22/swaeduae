<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \App\Events\VolunteerCheckedOut::class => [
            \App\Listeners\GenerateCertificate::class,
        ],
    ];

    public function boot()
    {
        parent::boot();
    }
}
