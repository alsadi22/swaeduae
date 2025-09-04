<div class="container py-4">
    <h2 class="mb-3">{{ __('Opportunities') }}</h2>
    <div class="row g-3">
        @forelse($opportunities as $o)
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $o->title ?? ('#'.$o->id) }}</h5>
                        <p class="card-text text-muted">{{ \Illuminate\Support\Str::limit($o->description ?? '', 120) }}</p>
                        <a href="{{ url('/opportunities/'.$o->id) }}" class="btn btn-sm btn-primary">{{ __('View') }}</a>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">{{ __('No opportunities found.') }}</p>
        @endforelse
    </div>
</div>
