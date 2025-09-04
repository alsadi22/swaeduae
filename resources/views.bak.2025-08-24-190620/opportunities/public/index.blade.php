@extends('layouts.app')
@section('title','Opportunities')
@section('content')
<div class="container my-4">
  <h1 class="h4 mb-3">{{ __('Opportunities') }}</h1>
  <form method="get" class="mb-3">
    <div class="row g-2">
      <div class="col-sm-6"><input class="form-control" name="q" value="{{ $q }}" placeholder="Search"></div>
      <div class="col-auto"><button class="btn btn-primary">Search</button></div>
    </div>
  </form>

  @if($rows instanceof \Illuminate\Contracts\Pagination\Paginator ? $rows->count() : collect($rows)->count())
    <div class="row g-3">
      @foreach($rows as $o)
        <div class="col-md-4">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title">{{ $o->title ?? $o->name ?? ('#'.$o->id) }}</h5>
              @php $d = $o->date ?? $o->start_date ?? $o->event_date ?? $o->starts_at ?? null; @endphp
              @if($d)<div class="small text-muted mb-1">{{ \Illuminate\Support\Carbon::parse($d)->toFormattedDateString() }}</div>@endif
              <div class="small text-muted">{{ trim(($o->city ?? '').' '.(($o->region ?? $o->emirate ?? '') ? '· '.($o->region ?? $o->emirate) : '')) }}</div>
              <a href="{{ route('opps.public.show',$o->id) }}" class="btn btn-sm btn-outline-primary mt-2">View</a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
    @if(method_exists($rows,'links')) <div class="mt-3">{{ $rows->links() }}</div> @endif
  @else
    <p class="text-muted">No opportunities found.</p>
  @endif
</div>
@endsection
