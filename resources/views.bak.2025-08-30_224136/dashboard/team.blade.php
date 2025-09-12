@extends('layouts.app')

@section('title','Team Dashboard')

@section('content')
<h1>Team Dashboard</h1>
@if($team)
    <p>Team: {{ $team->name }}</p>
@else
    <p>You are not part of a team yet.</p>
@endif
@endsection
