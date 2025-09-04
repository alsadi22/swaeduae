@extends('layouts.app')

@section('title','Top Volunteers')

@section('content')
<h1>Top Volunteers</h1>
<ol>
@foreach($topVolunteers as $entry)
    <li>{{ $entry->volunteer->name ?? 'Unknown' }} – {{ $entry->total_hours }} hours</li>
@endforeach
</ol>
@endsection
