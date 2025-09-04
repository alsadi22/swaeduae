@extends('public.layout-travelpro')
@section('title','My Applications')
@section('content')
<section class="section"><div class="container" style="max-width:960px">
  @php($apps = $apps ?? collect())
  <h2 class="mb-3">My Applications</h2>
  @if(session("status"))
    <div class="alert alert-success small">{{ session("status") }}</div>
  @endif
  @if($apps->isEmpty())
    <p class="text-muted">No applications found.</p>
  @else
  <div class="table-responsive">
  <table class="table table-sm align-middle">
    <thead><tr>
      <th>Event</th><th>City</th><th>Starts</th><th>Ends</th><th>Status</th><th>Actions</th>
    </tr></thead>
    <tbody>
      @foreach($apps as $a)
      <tr>
        <td><a href="{{ url('/opportunities/'.$a->slug) }}">{{ $a->title }}</a></td>
        <td>{{ $a->location ?? '—' }}</td>
        <td>{{ optional($a->starts_at)->format('Y-m-d H:i') ?? '—' }}</td>
        <td>{{ optional($a->ends_at)->format('Y-m-d H:i') ?? '—' }}</td>
        <td><span class="badge text-bg-secondary">{{ $a->status }}</span></td>
        <td class="text-nowrap">
          @if($a->slug)
          <a class="btn btn-sm btn-outline-primary" href="{{ url('/ics/'.$a->slug) }}">ICS</a>
          <a class="btn btn-sm btn-outline-secondary" target="_blank"
             href="https://wa.me/?text={{ urlencode($a->title.' — '.url('/opportunities/'.$a->slug)) }}">Share</a>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  </div>
  @endif
</div></section>
@endsection
