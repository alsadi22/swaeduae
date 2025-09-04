@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Volunteer Opportunities</h1>

    @if($opportunities->count())
        <div class="row">
            @foreach($opportunities as $opportunity)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        @if($opportunity->image)
                            <img src="{{ asset('storage/' . $opportunity->image) }}" class="card-img-top" alt="{{ $opportunity->title }}">
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $opportunity->title }}</h5>
                            <p class="card-text">{{ \Illuminate\Support\Str::limit($opportunity->description, 100) }}</p>
                            <p class="mb-1"><strong>Date:</strong> {{ \Carbon\Carbon::parse($opportunity->start_date)->format('M d, Y') }}</p>
                            <p class="mb-3"><strong>Location:</strong> {{ $opportunity->region }}</p>
                            <a href="{{ route('opportunities.show', $opportunity->id) }}" class="btn btn-primary mt-auto">View Details</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="d-flex justify-content-center">
            {{ $opportunities->links() }}
        </div>
    @else
        <p>No volunteer opportunities found.</p>
    @endif
</div>
@endsection