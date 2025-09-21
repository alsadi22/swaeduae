@extends('public.layout')
@section('title','My Account')
@section('content')
<section class="section"><div class="container" style="max-width:920px">
  <h2 class="mb-3">My Account</h2>

  <div class="row g-3">
    <div class="col-md-4"><div class="feature-card"><h5>Total Hours</h5><div class="h4 mb-0">{{ $totals['hours'] }}</div></div></div>
    <div class="col-md-4"><div class="feature-card"><h5>Certificates</h5><div class="h4 mb-0">{{ $totals['certs'] }}</div></div></div>
    <div class="col-md-4"><div class="feature-card"><h5>Last Activity</h5><div class="h4 mb-0">{{ $totals['last'] ?: '—' }}</div></div></div>
  </div>

  <h3 class="h5 mt-4 mb-2">My Certificates</h3>
  @if($certs->isEmpty())
    <p class="text-muted">No certificates yet.</p>
  @else
    <div class="op-grid">
      @foreach($certs as $c)
        <article class="op-card">
          <div class="op-card__body">
            <div class="small-muted">Issued: {{ optional($c->issued_at)->format('Y-m-d') ?? '—' }}</div>
            <div class="mt-2">
              <a class="btn btn-sm btn-outline-primary" href="{{ url('/certificates/'.$c->uuid.'.pdf') }}">Download PDF</a>
            </div>
          </div>
        </article>
      @endforeach
    </div>
  @endif
</div></section>
@endsection
