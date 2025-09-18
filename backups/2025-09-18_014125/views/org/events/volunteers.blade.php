@extends('layout.layout')

@section('content')
<div class="container py-5">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0">Volunteers · Opportunity #{{ $opportunityId }}</h1>
    <a class="btn btn-primary"
       href="{{ url('org/opportunities/'.$opportunityId.'/applicants.csv') }}">
       Download CSV
    </a>
  </div>

  @php
    // Normalize rows whether they came from event_registrations or applications join
    $norm = $rows->map(function($r){
      return (object)[
        'application_id'   => $r->application_id ?? $r->id ?? null,
        'status'           => $r->status        ?? ($r->application_status ?? 'submitted'),
        'applied_at'       => $r->applied_at    ?? ($r->created_at ?? null),
        'volunteer_name'   => $r->volunteer_name?? ($r->name  ?? ($r->volunteer_name ?? '')),
        'volunteer_email'  => $r->volunteer_email?? ($r->email ?? ($r->volunteer_email ?? '')),
        'user_id'          => $r->user_id       ?? null,
      ];
    });
  @endphp

  @if ($norm->isEmpty())
    <div class="alert alert-info">No applicants yet.</div>
  @else
  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <th>#</th>
          <th>Volunteer</th>
          <th>Email</th>
          <th>Status</th>
          <th>Applied At</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($norm as $i => $r)
        <tr>
          <td>{{ $r->application_id ?? ('#'.($i+1)) }}</td>
          <td>{{ $r->volunteer_name }}</td>
          <td><a href="mailto:{{ $r->volunteer_email }}">{{ $r->volunteer_email }}</a></td>
          <td><span class="badge bg-secondary">{{ $r->status }}</span></td>
          <td>{{ \Illuminate\Support\Carbon::parse($r->applied_at)->format('Y-m-d H:i') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>
@endsection
