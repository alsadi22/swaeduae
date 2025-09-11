@extends(public.layout)
@section('title', $op->title ?? 'Opportunity')
@section('content')
<section class="section"><div class="container" style="max-width:960px">
  <a class="btn btn-sm btn-outline-secondary mb-3" href="{{ url('/opportunities') }}">&larr; All opportunities</a>
  <h2 class="mb-2">{{ $op->title ?? 'Opportunity' }}</h2>
  <p class="text-muted mb-3">
    <span>{{ $op->location ?? '—' }}</span>
    @if($op->starts_at) • {{ \Illuminate\Support\Carbon::parse($op->starts_at)->format('Y-m-d H:i') }} @endif
    @if($op->ends_at) – {{ \Illuminate\Support\Carbon::parse($op->ends_at)->format('Y-m-d H:i') }} @endif
  </p>
  @auth
  <form method="POST" action="{{ url('/opportunities/'.$op->slug.'/apply') }}" class="mt-2 mb-4">
    @csrf
    <button class="btn btn-primary btn-sm">Apply</button>
    <a class="btn btn-outline-secondary btn-sm" href="{{ url('/ics/'.$op->slug) }}">ICS</a>
  </form>
  @else
  <p><a class="btn btn-outline-primary btn-sm" href="{{ url('/login') }}">Sign in to apply</a></p>
  @endauth
  <div class="card"><div class="card-body"><p>No description yet.</p></div></div>
</div></section>
@endsection
