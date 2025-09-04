@extends('layouts.app')

@section('title','Admin Dashboard')

@section('content')
<h1>Admin Dashboard</h1>
<ul>
    <li>Total Volunteers: {{ $volunteerCount }}</li>
    <li>Total Organizations: {{ $organizationCount }}</li>
    <li>Total Opportunities: {{ $opportunityCount }}</li>
</ul>
@endsection
