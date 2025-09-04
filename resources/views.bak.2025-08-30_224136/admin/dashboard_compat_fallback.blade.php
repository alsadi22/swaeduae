@extends('layouts.admin')
@section('content')
<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <h4 class="mb-3">Admin dashboard (compat mode)</h4>
      <p class="text-muted mb-3">Rendering legacy dashboard failed; showing a safe summary.</p>
      <table class="table table-sm">
        <thead><tr><th>Entity</th><th class="text-end">Count</th></tr></thead>
        <tbody>
          @foreach(($counts ?? []) as $k => $v)
            <tr><td>{{ $k }}</td><td class="text-end">{{ $v }}</td></tr>
          @endforeach
        </tbody>
      </table>
      <div class="mt-3 d-flex gap-2">
        <a class="btn btn-primary" href="{{ url('/admin/users') }}">Users</a>
        <a class="btn btn-primary" href="{{ url('/admin/opportunities') }}">Opportunities</a>
        <a class="btn btn-outline-secondary" href="{{ url('/admin/_diag') }}">Diagnostics</a>
      </div>
      @isset($error)
        <div class="alert alert-warning mt-4">
          <strong>Legacy view error:</strong> {{ $error['m'] ?? 'unknown' }}
          <div class="small text-muted">in {{ $error['f'] ?? '?' }}:{{ $error['l'] ?? '?' }}</div>
        </div>
      @endisset
    </div>
  </div>
</div>
@endsection
