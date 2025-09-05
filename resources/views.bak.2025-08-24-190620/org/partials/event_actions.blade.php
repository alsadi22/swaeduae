<div class="btn-group btn-group-sm" role="group" aria-label="Event actions">
    <a href="{{ route('org.events.attendance.index', $event) }}" class="btn btn-primary">
        {{ __('Manage Attendance') }}
    </a>
    @if (Route::has('org.events.volunteers.csv'))
        <a href="{{ route('org.events.volunteers.csv', $event) }}" class="btn btn-outline-secondary">
            {{ __('Export CSV') }}
        </a>
    @endif
</div>
