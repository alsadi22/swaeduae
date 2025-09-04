@extends('layouts.app')
@section('content')
<div class="container">
  <h1>Leaderboard</h1>
  <table class="table">
    <thead><tr><th>#</th><th>Volunteer</th><th>Total Hours</th></tr></thead>
    <tbody>
      @foreach($leaders as $index => $leader)
        <tr><td>{{ $index+1 }}</td><td>{{ $leader->name }}</td><td>{{ $leader->hours_sum }}</td></tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
