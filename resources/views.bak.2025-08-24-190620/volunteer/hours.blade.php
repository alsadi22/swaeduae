@extends('layouts.app')
@section('content')
<div class="container">
  <h1>My Hours</h1>
  <table class="table">
    <thead><tr><th>Event</th><th>Date</th><th>Hours</th></tr></thead>
    <tbody>
      @forelse($hours as $record)
        <tr><td>{{ $record->event->title }}</td><td>{{ $record->event->date->format('d M Y') }}</td><td>{{ $record->hours }}</td></tr>
      @empty
        <tr><td colspan="3" class="text-muted">No hours recorded.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
