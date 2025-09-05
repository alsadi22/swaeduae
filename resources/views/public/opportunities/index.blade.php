@extends('public.layout-travelpro')
@section('title','Opportunities')
@section('content')
<section class="section"><div class="container" style="max-width:960px">
  <h2 class="mb-3">Opportunities</h2>
  @if($items->isEmpty())
    <p class="text-muted">No opportunities yet.</p>
  @else
    <div class="list-group">
      @foreach($items as $o)
      <a class="list-group-item list-group-item-action d-flex justify-content-between" href="{{ url('/opportunities/'.$o->slug) }}">
        <span><strong>{{ $o->title }}</strong>
          <small class="text-muted d-block">{{ $o->location ?? '—' }} @if($o->starts_at) • {{ $o->starts_at->format('Y-m-d H:i') }} @endif</small>
        </span>
        <span class="badge text-bg-light">View</span>
      </a>
      @endforeach
    </div>
    <div class="mt-3">{{ $items->links() }}</div>
  @endif
</div></section>
@endsection
