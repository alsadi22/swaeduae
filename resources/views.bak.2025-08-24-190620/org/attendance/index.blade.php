@extends('org.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('Attendance') }} — {{ $event->title ?? $event->name ?? ('#'.$event->id) }}</h3>
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('org.events.finalize', $event) }}" onsubmit="return confirm('{{ __('Finalize attendance and issue certificates? This cannot be undone.') }}')">
                @csrf
                <button class="btn btn-success btn-sm">{{ __('Finalize & Issue Certificates') }}</button>
            </form>
            <a href="{{ url('/org') }}" class="btn btn-outline-secondary btn-sm">{{ __('Back to Dashboard') }}</a>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('#') }}</th>
                            <th>{{ __('Volunteer') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th class="text-center">{{ __('Check-In') }}</th>
                            <th class="text-center">{{ __('Check-Out') }}</th>
                            <th class="text-center">{{ __('No-Show') }}</th>
                            <th style="width: 180px;">{{ __('Minutes') }}</th>
                            <th>{{ __('Reason') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $i => $a)
                        <tr>
                            <td>{{ $attendances->firstItem() + $i }}</td>
                            <td>{{ $a->user->name ?? ('#'.$a->user_id) }}</td>
                            <td class="text-muted small">{{ $a->user->email ?? '' }}</td>

                            <td class="text-center">
                                <form method="POST" action="{{ route('org.attendances.check', $a) }}">
                                    @csrf
                                    <input type="hidden" name="action" value="in">
                                    <button class="btn btn-outline-primary btn-xs">{{ __('Check-In') }}</button>
                                </form>
                            </td>
                            <td class="text-center">
                                <form method="POST" action="{{ route('org.attendances.check', $a) }}">
                                    @csrf
                                    <input type="hidden" name="action" value="out">
                                    <button class="btn btn-outline-primary btn-xs">{{ __('Check-Out') }}</button>
                                </form>
                            </td>

                            <td class="text-center">
                                <form method="POST" action="{{ route('org.attendances.no_show', $a) }}">
                                    @csrf
                                    <input type="hidden" name="no_show" value="{{ isset($a->no_show) ? (int)!$a->no_show : 1 }}">
                                    <button class="btn btn-outline-warning btn-xs">
                                        {{ (isset($a->no_show) && $a->no_show) ? __('Unset') : __('Mark') }}
                                    </button>
                                </form>
                            </td>

                            <td>
                                <form class="d-flex" method="POST" action="{{ route('org.attendances.minutes', $a) }}">
                                    @csrf
                                    <input type="number" name="minutes" min="0" step="1" class="form-control form-control-sm me-2"
                                           value="{{ (int)($a->minutes ?? 0) }}">
                                    <button class="btn btn-primary btn-sm">{{ __('Save') }}</button>
                                </form>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('org.attendances.minutes', $a) }}">
                                    @csrf
                                    <input type="hidden" name="minutes" value="{{ (int)($a->minutes ?? 0) }}">
                                    <input type="text" name="reason" class="form-control form-control-sm"
                                           value="{{ $a->minutes_reason ?? '' }}" placeholder="{{ __('Optional reason') }}"
                                           onblur="this.form.submit()">
                                </form>
                            </td>

                            <td class="text-end">
                                <form method="POST" action="{{ route('org.attendances.cert.resend', $a) }}">
                                    @csrf
                                    <button class="btn btn-outline-success btn-sm">{{ __('Resend Certificate') }}</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center py-4 text-muted">{{ __('No registrations yet.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
