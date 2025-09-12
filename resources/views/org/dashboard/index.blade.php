@extends('org.layout')
@section('title', __('Organization Dashboard'))

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">{{ __('Dashboard') }}</h2>
    <form class="d-flex gap-2" method="GET">
      <input type="date" name="start" class="form-control" style="max-width: 180px" value="{{ $stats['start'] ?? '' }}">
      <input type="date" name="end" class="form-control" style="max-width: 180px" value="{{ $stats['end'] ?? '' }}">
      <button class="btn btn-primary">{{ __('Apply') }}</button>
      <a href="{{ route('org.dashboard.guard.wrap') }}" class="btn btn-outline-secondary">{{ __('Reset') }}</a>
    </form>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card shadow-sm h-100"><div class="card-body">
        <div class="text-muted small">{{ __('Active Volunteers') }}</div>
        <div class="fs-3 fw-bold">{{ $stats['active_volunteers'] ?? 0 }}</div>
      </div></div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm h-100"><div class="card-body">
        <div class="text-muted small">{{ __('Total Opportunities') }}</div>
        <div class="fs-3 fw-bold">{{ $stats['total_opportunities'] ?? 0 }}</div>
      </div></div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm h-100"><div class="card-body">
        <div class="text-muted small">{{ __('Pending Approvals') }}</div>
        <div class="fs-3 fw-bold">{{ $stats['pending_approvals'] ?? 0 }}</div>
      </div></div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm h-100"><div class="card-body">
        <div class="text-muted small">{{ __('Total Hours') }}</div>
        <div class="fs-3 fw-bold">{{ $stats['total_hours'] ?? 0 }}</div>
      </div></div>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <strong>{{ __('Latest Opportunities') }}</strong>
      <a href="{{ url('/org/opportunities') }}" class="btn btn-sm btn-outline-primary">{{ __('Manage All') }}</a>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>ID</th>
              <th>{{ __('Title') }}</th>
              <th>{{ __('Emirate') }}</th>
              <th>{{ __('Status') }}</th>
              <th>{{ __('Start') }}</th>
              <th>{{ __('End') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($latest_opps ?? [] as $o)
              <tr>
                <td>{{ $o->id }}</td>
                <td>{{ $o->title ?? '-' }}</td>
                <td>{{ $o->emirate ?? '-' }}</td>
                <td><span class="badge bg-secondary">{{ $o->status ?? '-' }}</span></td>
                <td>{{ \Illuminate\Support\Str::of($o->start_date ?? '')->limit(10) }}</td>
                <td>{{ \Illuminate\Support\Str::of($o->end_date ?? '')->limit(10) }}</td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center text-muted py-4">{{ __('No opportunities found.') }}</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
