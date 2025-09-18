@component('mail::message')
# لقد غادرت {{ $event->title ?? 'الفعالية' }}

آخر تواجد لك كان {{ $lastSeen->toDayDateTimeString() }}.

يرجى فتح التطبيق للمتابعة.

شكراً،
سواعد الإمارات
@endcomponent
