@extends('org.layout')

@section('title', __('Shortlisting'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0">{{ __('Shortlisting') }} — {{ $opportunity->title ?? ('#'.$opportunity->id) }}</h1>
            <small class="text-muted">
                {{ __('Manage applicants: approve, waitlist, reject. Auto-promote from waitlist when capacity allows.') }}
            </small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form class="d-flex" method="GET">
                <input type="text" name="s" value="{{ request('s') }}" placeholder="{{ __('Search name/email') }}" class="form-control form-control-sm me-2">
                <select name="status" class="form-select form-select-sm me-2">
                    @php $st = request('status'); @endphp
                    <option value="">{{ __('All statuses') }}</option>
                    @foreach(['pending','approved','waitlist','rejected'] as $opt)
                        <option value="{{ $opt }}" @if($st===$opt) selected @endif>{{ ucfirst($opt) }}</option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-outline-secondary">{{ __('Filter') }}</button>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card shadow-sm"><div class="card-body">
                <div class="text-uppercase text-muted small">{{ __('Slot Cap') }}</div>
                <form method="POST" action="{{ route('org.shortlist.slot_cap', $opportunity->id) }}" class="d-flex gap-2">
                    @csrf
                    <input type="number" name="slot_cap" class="form-control form-control-sm" min="0" value="{{ $cap }}" placeholder="{{ __('Unlimited') }}">
                    <button class="btn btn-sm btn-outline-primary">{{ __('Save') }}</button>
                </form>
                <small class="text-muted d-block mt-2">{{ __('Available') }}:
                    <strong>{{ is_string($available) ? $available : (int)$available }}</strong>
                </small>
            </div></div>
        </div>
        <div class="col-md-9">
            <div class="card shadow-sm"><div class="card-body">
                <div class="row text-center">
                    <div class="col"><div class="text-muted small">{{ __('Total') }}</div><div class="h5 mb-0">{{ (int)($counts->total ?? 0) }}</div></div>
                    <div class="col"><div class="text-muted small">{{ __('Approved') }}</div><div class="h5 mb-0">{{ (int)($counts->approved ?? 0) }}</div></div>
                    <div class="col"><div class="text-muted small">{{ __('Waitlist') }}</div><div class="h5 mb-0">{{ (int)($counts->waitlist ?? 0) }}</div></div>
                    <div class="col"><div class="text-muted small">{{ __('Pending') }}</div><div class="h5 mb-0">{{ (int)($counts->pending ?? 0) }}</div></div>
                    <div class="col"><div class="text-muted small">{{ __('Rejected') }}</div><div class="h5 mb-0">{{ (int)($counts->rejected ?? 0) }}</div></div>
                </div>
            </div></div>
        </div>
    </div>

    @if(session('status')) <div class="alert alert-success">{{ session('status') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <form method="POST" action="{{ route('org.shortlist.bulk', $opportunity->id) }}" id="bulkForm">
                @csrf
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:32px;"><input type="checkbox" onclick="document.querySelectorAll('.rowchk').forEach(cb=>cb.checked=this.checked)"></th>
                                <th>#</th>
                                <th>{{ __('Volunteer') }}</th>
                                <th>{{ __('Applied At') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($apps as $a)
                            <tr>
                                <td><input type="checkbox" class="rowchk" name="ids[]" value="{{ $a->id }}"></td>
                                <td>{{ $a->id }}</td>
                                <td>{{ $a->user_name }}<br><small class="text-muted">{{ $a->user_email }}</small></td>
                                <td>{{ \Illuminate\Support\Carbon::parse($a->created_at)->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</td>
                                <td>
                                    <span class="badge
                                        @if($a->status==='approved') bg-success
                                        @elseif($a->status==='waitlist') bg-warning
                                        @elseif($a->status==='rejected') bg-danger
                                        @else bg-secondary @endif">
                                        {{ $a->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted p-4">{{ __('No applications yet.') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex align-items-center gap-2 p-3 border-top">
                    <select name="action" class="form-select form-select-sm" style="max-width:220px;">
                        <option value="approve">{{ __('Approve') }}</option>
                        <option value="waitlist">{{ __('Waitlist') }}</option>
                        <option value="reject">{{ __('Reject') }}</option>
                        <option value="pending">{{ __('Mark Pending') }}</option>
                    </select>
                    <button class="btn btn-sm btn-primary">{{ __('Apply to selected') }}</button>
                    <span class="text-muted ms-2 small">{{ __('Auto-promotes from waitlist to fill capacity.') }}</span>
                </div>

            </form>
        </div>
        <div class="card-footer">
            {{ $apps->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
