
@if($similar->count())
<hr>
<h3 class="mt-3 mb-2">{{ __('messages.similar_opportunities') }}</h3>
<div class="row">
  @foreach($similar as $s)
    <div class="col-md-4 mb-3">
      <div class="card h-100 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">{{ $s->title }}</h5>
          <p class="card-text text-muted">{{ \Illuminate\Support\Str::limit($s->description, 120) }}</p>
          <a class="btn btn-outline-primary btn-sm" href="{{ route('opportunities.show', $s->id) }}">{{ __('messages.view') }}</a>
        </div>
      </div>
    </div>
  @endforeach
</div>
@endif
