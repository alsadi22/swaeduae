@extends('admin.layout')

@section('title', __('Applications Review'))

@section('content')
<div class="container-fluid py-4">
    <h1 class="mb-3" style="font-weight:700;">{{ __('Applications') }} / الطلبات</h1>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
    @isset($message) <div class="alert alert-warning">{{ $message }}</div> @endisset

    <form class="card p-3 mb-3" method="GET" action="{{ url('/admin/applications') }}">
        <div class="row g-2">
            <div class="col-sm-3">
                <select name="status" class="form-select">
                    <option value="">{{ __('All statuses') }}</option>
                    @foreach(['pending','approved','waitlisted','rejected','cancelled'] as $s)
                        <option value="{{ $s }}" @selected(($filters['status'] ?? '')==$s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6">
                <input class="form-control" type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="{{ __('Name, email, opportunity...') }}">
            </div>
            <div class="col-sm-3">
                <button class="btn btn-primary w-100" style="background:#9CAFAA;border-color:#9CAFAA;">{{ __('Filter') }} / تصفية</button>
            </div>
        </div>
    </form>

    <form method="POST" action="{{ url('/admin/applications/bulk') }}">
        @csrf
        <div class="d-flex gap-2 mb-2">
            <button name="action" value="approve"   class="btn btn-success btn-sm">{{ __('Approve (capacity-aware)') }}</button>
            <button name="action" value="waitlist"  class="btn btn-warning btn-sm">{{ __('Waitlist') }}</button>
            <button name="action" value="reject"    class="btn btn-danger btn-sm">{{ __('Reject') }}</button>
            <button name="action" value="pending"   class="btn btn-secondary btn-sm">{{ __('Mark Pending') }}</button>
            <button name="action" value="cancelled" class="btn btn-outline-secondary btn-sm">{{ __('Mark Cancelled') }}</button>
        </div>

        <div class="table-responsive card">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th><input type="checkbox" onclick="document.querySelectorAll('.chk').forEach(c=>c.checked=this.checked)"></th>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Opportunity') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Applied') }}</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($apps as $a)
                    <tr>
                        <td><input type="checkbox" class="chk" name="ids[]" value="{{ $a->id }}"></td>
                        <td>{{ $a->user_name ?? '-' }}</td>
                        <td class="text-muted">{{ $a->email ?? '-' }}</td>
                        <td><a href="{{ url('/opportunities/'.($a->opportunity_id)) }}">{{ $a->opportunity_title ?? ('#'.$a->opportunity_id) }}</a></td>
                        <td>
                            <span class="badge bg-{{ $a->status=='approved' ? 'success' : ($a->status=='rejected' ? 'danger' : ($a->status=='waitlisted' ? 'warning' : 'secondary')) }}">
                                {{ ucfirst($a->status ?? 'pending') }}
                            </span>
                        </td>
                        <td class="text-muted">{{ \Carbon\Carbon::parse($a->created_at)->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-muted p-4">{{ __('No applications found.') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <div class="mt-3">
        {{ $apps->links() }}
    </div>
</div>
@endsection
