@component('mail::message')
# متطوع غادر {{ $event->title ?? 'الفعالية' }}

آخر تواجد كان {{ $lastSeen->toDayDateTimeString() }}.

@endcomponent
