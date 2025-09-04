@extends('admin.layout')
@section('content')
<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <h4 class="mb-3">Admin dashboard (safe mode)</h4>
      <p class="mb-2">Signed in as <strong>{{ optional(auth()->user())->email }}</strong></p>
      <table class="table table-sm">
        <thead><tr><th>Entity</th><th>Count</th></tr></thead>
        <tbody>
          @foreach(($stats ?? []) as $k => $v)
            <tr><td>{{ $k }}</td><td>{{ $v }}</td></tr>
          @endforeach
        </tbody>
      </table>
      <div class="mt-3 d-flex gap-2">
        <a class="btn btn-primary" href="{{ url('/admin/users') }}">Users</a>
        <a class="btn btn-primary" href="{{ url('/admin/events') }}">Events</a>
        <a class="btn btn-primary" href="{{ url('/admin/opportunities') }}">Opportunities</a>
        <a class="btn btn-outline-secondary" href="{{ url('/admin/_diag') }}">Diagnostics</a>
      </div>
      @if(!empty($error))
        <div class="alert alert-warning mt-4">
          <strong>Legacy dashboard failed:</strong> {{ $error['m'] ?? 'unknown' }}
          <div class="small text-muted">in {{ $error['f'] ?? '?' }}:{{ $error['l'] ?? '?' }}</div>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
