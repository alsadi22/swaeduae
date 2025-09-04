@extends('layouts.app')

@section('title','Volunteer Dashboard')

@section('content')
<h1>Volunteer Dashboard</h1>
<p>Total Volunteer Hours: {{ $totalHours }}</p>

<h2>Upcoming Opportunities</h2>
@forelse($upcomingOpportunities as $opp)
    <div>{{ $opp->title }} - {{ $opp->start_date->format('Y-m-d H:i') }}</div>
@empty
    <p>No upcoming opportunities.</p>
@endforelse

<h2>Past Opportunities</h2>
@forelse($pastOpportunities as $opp)
    <div>{{ $opp->title }} - {{ $opp->start_date->format('Y-m-d H:i') }}</div>
@empty
    <p>No past opportunities.</p>
@endforelse

<h2>Badges</h2>
@forelse($badges as $badge)
    <div>{{ $badge->name }}</div>
@empty
    <p>No badges yet.</p>
@endforelse

<h2>Certificates</h2>
@forelse($certificates as $certificate)
    <div><a href="{{ asset($certificate->file_path) }}" target="_blank">Certificate #{{ $certificate->certificate_number }}</a></div>
@empty
    <p>No certificates yet.</p>
@endforelse
@endsection
