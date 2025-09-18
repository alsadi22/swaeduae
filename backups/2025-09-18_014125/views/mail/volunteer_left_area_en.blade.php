@component('mail::message')
# You left {{ $event->title ?? 'the event' }}

We last saw you at {{ $lastSeen->toDayDateTimeString() }}.

Please open the app to resume.

Thanks,
SwaedUAE
@endcomponent
