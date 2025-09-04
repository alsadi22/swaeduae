@extends('admin.layout')
@section('content')
<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <h4 class="mb-3">Admin diagnostics</h4>
      <p class="mb-1"><strong>User:</strong> {{ optional(auth()->user())->email }}</p>
      <p class="mb-3"><strong>Gate isAdmin:</strong> {{ Gate::allows('isAdmin') ? 'YES' : 'NO' }}</p>
      <table class="table table-sm">
        <thead><tr><th>Entity</th><th>Count</th></tr></thead>
        <tbody>
          @foreach($stats as $k=>$v)
            <tr><td>{{ $k }}</td><td>{{ $v }}</td></tr>
          @endforeach
        </tbody>
      </table>
      <a class="btn btn-primary" href="{{ route('admin.dashboard') }}">Go to real dashboard</a>
    </div>
  </div>
</div>
@endsection
