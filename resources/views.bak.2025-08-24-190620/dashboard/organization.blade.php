@extends('layouts.app')

@section('title','Organization Dashboard')

@section('content')
<h1>Organization Dashboard</h1>
<a href="{{ route('organization.opportunities.create') }}">Create New Opportunity</a>
<ul>
@forelse($opportunities as $opportunity)
    <li>{{ $opportunity->title }}</li>
@empty
    <li>No opportunities posted.</li>
@endforelse
</ul>
@endsection
