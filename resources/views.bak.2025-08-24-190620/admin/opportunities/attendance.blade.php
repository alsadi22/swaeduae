@extends('admin.layout')

@section('title', __('Attendance Management'))

@section('content')
<div class="container-fluid py-4">
    <h1 class="mb-3" style="font-weight:700;">{{ __('Attendance') }} — {{ $op->title ?? ('#'.$op->id) }}</h1>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('Check-in (by email)') }}</h5>
                    <form method="POST" action="{{ url('/admin/opportunities/'.$op->id.'/attendance/check-in') }}" class="d-flex gap-2">
                        @csrf
                        <input class="form-control" type="email" name="email" required placeholder="user@example.com">
                        <button class="btn btn-primary" style="background:#9CAFAA;border-color:#9CAFAA;">{{ __('Check-in') }}</button>
                    </form>

                    <hr>
                    <h5 class="mb-3">{{ __('Check-out (by email)') }}</h5>
                    <form method="POST" action="{{ url('/admin/opportunities/'.$op->id.'/attendance/check-out') }}" class="d-flex gap-2">
                        @csrf
                        <input class="form-control" type="email" name="email" required placeholder="user@example.com">
                        <button class="btn btn-outline-primary">{{ __('Check-out') }}</button>
                    </form>

                    <hr>
                    <a href="{{ url('/admin/opportunities/'.$op->id.'/scan') }}" class="btn btn-outline-secondary w-100">{{ __('Open Scanner') }}</a>
                    <hr>
                    <form method="POST" action="{{ url('/admin/opportunities/'.$op->id.'/attendance/finalize') }}">
                        @csrf
                        <button class="btn btn-success w-100">{{ __('Finalize Hours') }}</button>
                    </form>

                    <hr>
                    <h6 class="mb-2">{{ __('Manual hours adjustment') }}</h6>
                    <form method="POST" action="{{ url('/admin/opportunities/'.$op->id.'/attendance/adjust') }}" class="row g-2">
                        @csrf
                        <div class="col-7"><input class="form-control" type="email" name="email" placeholder="user@example.com" required></div>
                        <div class="col-3"><input class="form-control" type="number" name="minutes" min="0" placeholder="mins" required></div>
                        <div class="col-2"><button class="btn btn-outline-success w-100">{{ __('Set') }}</button></div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('Present (checked-in)') }}</h5>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr><th>{{ __('User ID') }}</th><th>{{ __('Check-in') }}</th></tr>
                            </thead>
                            <tbody>
                            @forelse($present as $p)
                                <tr>
                                    <td>#{{ $p->user_id }}</td>
                                    <td class="text-muted">{{ \Carbon\Carbon::parse($p->check_in_at)->format('d M Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-muted p-3">{{ __('No present records.') }}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('Completed (checked-out)') }}</h5>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr><th>{{ __('User ID') }}</th><th>{{ __('Check-in') }}</th><th>{{ __('Check-out') }}</th><th>{{ __('Minutes') }}</th></tr>
                            </thead>
                            <tbody>
                            @forelse($completed as $c)
                                @php
                                    $mins = \Carbon\Carbon::parse($c->check_out_at)->diffInMinutes(\Carbon\Carbon::parse($c->check_in_at));
                                @endphp
                                <tr>
                                    <td>#{{ $c->user_id }}</td>
                                    <td class="text-muted">{{ \Carbon\Carbon::parse($c->check_in_at)->format('d M Y H:i') }}</td>
                                    <td class="text-muted">{{ \Carbon\Carbon::parse($c->check_out_at)->format('d M Y H:i') }}</td>
                                    <td>{{ $mins }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted p-3">{{ __('No completed records.') }}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('No-shows (approved but no attendance)') }}</h5>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr><th>{{ __('Application ID') }}</th><th>{{ __('User ID') }}</th><th>{{ __('Applied At') }}</th></tr>
                            </thead>
                            <tbody>
                            @forelse($noShows as $n)
                                <tr>
                                    <td>#{{ $n->id }}</td>
                                    <td>#{{ $n->user_id }}</td>
                                    <td class="text-muted">{{ \Carbon\Carbon::parse($n->created_at)->format('d M Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted p-3">{{ __('No no-shows detected.') }}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
