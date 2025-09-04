@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Welcome, {{ auth()->user()->name }}</h1>
    <div class="row my-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3>{{ auth()->user()->volunteerHours->sum('minutes') / 60 }} hrs</h3>
                    <p>Total Volunteer Hours</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
