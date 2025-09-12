@component('mail::message')
# Volunteer left {{ $event->title ?? 'the event' }}

Last seen at {{ $lastSeen->toDayDateTimeString() }}.

@endcomponent
