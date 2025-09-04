@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">{{ $opportunity->title }}</h1>

    <div class="mb-4">
        @if($opportunity->image)
            <img src="{{ asset('storage/' . $opportunity->image) }}" class="img-fluid mb-3" alt="{{ $opportunity->title }}">
        @endif
        <p>{{ $opportunity->description }}</p>
        <p><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($opportunity->start_date)->format('M d, Y') }}</p>
        <p><strong>End Date:</strong> {{ \Carbon\Carbon::parse($opportunity->end_date)->format('M d, Y') }}</p>
        <p><strong>Location:</strong> {{ $opportunity->region }}</p>
        <p><strong>Max Volunteers:</strong> {{ $opportunity->max_volunteers }}</p>
    </div>
    <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
</div>
@endsection