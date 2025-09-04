@php($t = app()->getLocale() === 'ar' ? 'en' : 'ar')
<a href="{{ url('/'.$t) }}" rel="nofollow">{{ strtoupper($t) }}</a>
