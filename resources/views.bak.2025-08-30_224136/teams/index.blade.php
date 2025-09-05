@extends('layouts.app')

@section('content')
<h2>Your Teams</h2>

@if(session('success'))
    <div class="bg-green-200 p-4 rounded mb-4">{{ session('success') }}</div>
@endif

<a href="{{ route('teams.create') }}" class="bg-primary text-white px-4 py-2 rounded mb-4 inline-block">Create New Team</a>

<ul>
@foreach($teams as $team)
    <li><a href="{{ route('teams.show', $team) }}" class="text-blue-600 underline">{{ $team->name }}</a></li>
@endforeach
</ul>
@endsection
