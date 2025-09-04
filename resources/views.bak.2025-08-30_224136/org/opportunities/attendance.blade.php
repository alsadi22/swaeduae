@extends('org.layout')

@section('title', __('Manage Attendance'))

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h1 class="h4 mb-0">{{ __('Attendance') }} — {{ $opportunity->title ?? ('#'.$opportunity->id) }}</h1>
            <small class="text-muted">{{ __('Check-in/out via QR is active; finalize to post hours.') }}</small>
        </div>
        <div class="col-auto">
            <form method="POST" action="{{ route('org.attendance.finalize', $opportunity->id) }}"
                  onsubmit="return confirm('{{ __('Finalize hours? This will lock attendance and post minutes to volunteer hours.') }}')">
                @csrf
                <button class="btn btn-primary btn-sm">{{ __('Finalize Hours') }}</button>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-uppercase text-muted small">{{ __('Total Check-ins') }}</div>
                    <div class="h4 mb-0">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-uppercase text-muted small">{{ __('Present') }}</div>
                    <div class="h4 mb-0">{{ $stats['present'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-uppercase text-muted small">{{ __('No-Shows') }}</div>
                    <div class="h4 mb-0">{{ $stats['no_show'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-uppercase text-muted small">{{ __('Finalized') }}</div>
                    <div class="h4 mb-0">{{ $stats['finalized'] }}</div>
                </div>
            </div>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('Volunteer') }}</th>
                            <th>{{ __('Check-in') }}</th>
                            <th>{{ __('Check-out') }}</th>
                            <th>{{ __('Minutes') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($attendances as $row)
                        <tr>
                            <td>{{ $row->id }}</td>
                            <td>
                                {{ $row->user->name ?? ('#'.$row->user_id) }}<br>
                                <small class="text-muted">{{ $row->user->email ?? '' }}</small>
                            </td>
                            <td>
                                @if($row->checkin_at)
                                    {{ \Illuminate\Support\Carbon::parse($row->checkin_at)->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
                                    <br><small class="text-muted">
                                        {{ $row->checkin_lat }}, {{ $row->checkin_lng }}
                                    </small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($row->checkout_at)
                                    {{ \Illuminate\Support\Carbon::parse($row->checkout_at)->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
                                    <br><small class="text-muted">
                                        {{ $row->checkout_lat }}, {{ $row->checkout_lng }}
                                    </small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td style="max-width:140px;">
                                @if(!$row->is_locked)
                                <form method="POST" action="{{ route('org.attendance.minutes.update', $opportunity->id) }}" class="d-flex gap-2">
                                    @csrf
                                    <input type="hidden" name="attendance_id" value="{{ $row->id }}">
                                    <input type="number" name="minutes" class="form-control form-control-sm" min="0" max="1440" value="{{ (int)$row->minutes }}">
                                    <button class="btn btn-outline-primary btn-sm">{{ __('Save') }}</button>
                                </form>
                                @else
                                    {{ (int)$row->minutes }}
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $row->status === 'no_show' ? 'bg-danger' : 'bg-success' }}">
                                    {{ $row->status }}
                                </span>
                                @if($row->is_locked)
                                    <span class="badge bg-secondary">{{ __('Locked') }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if(!$row->is_locked)
                                <form method="POST" action="{{ route('org.attendance.no_show.toggle', $opportunity->id) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="attendance_id" value="{{ $row->id }}">
                                    <button class="btn btn-sm btn-outline-warning">
                                        {{ $row->status === 'no_show' ? __('Mark Present') : __('Mark No-Show') }}
                                    </button>
                                </form>
                                @else
                                    <button class="btn btn-sm btn-outline-secondary" disabled>{{ __('Locked') }}</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted p-4">{{ __('No attendance yet.') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $attendances->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
