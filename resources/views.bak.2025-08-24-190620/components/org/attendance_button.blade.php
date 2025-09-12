@php
    $url = null;
    if (isset($opportunity) && Route::has('org.opportunities.attendance.index')) {
        $url = route('org.opportunities.attendance.index', $opportunity);
    } elseif (isset($event) && Route::has('org.events.attendance.index')) {
        $url = route('org.events.attendance.index', $event);
    }
@endphp

@if($url)
<a href="{{ $url }}" class="btn btn-primary btn-sm">
    {{ __('Manage Attendance') }}
</a>
@endif
