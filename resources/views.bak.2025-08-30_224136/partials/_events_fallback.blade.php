<div class="container py-4">
    <h2 class="mb-3">{{ __('Events') }}</h2>
    <div class="row g-3">
        @forelse($events as $e)
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $e->title ?? ('#'.$e->id) }}</h5>
                        <p class="card-text text-muted">{{ \Illuminate\Support\Str::limit($e->description ?? '', 120) }}</p>
                        <a href="{{ url('/events/'.$e->id) }}" class="btn btn-sm btn-primary">{{ __('View') }}</a>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">{{ __('No events found.') }}</p>
        @endforelse
    </div>
</div>
