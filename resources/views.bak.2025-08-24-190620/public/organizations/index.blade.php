@extends('layouts.app')

@section('content')@include('partials.page_header', ['title'=>__('Organizations')])
@include('partials.page_header', ['title'=>__('Organizations')])

  <section class="py-4">
    <div class="container">
      <h1 class="h4 mb-3">Organizations</h1>

      <form method="GET" class="row g-2 mb-3">
        <div class="col-12 col-md-6 col-lg-4">
          <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search name or email">
        </div>
        <div class="col-12 col-md-auto">
          <button class="btn btn-primary">Filter</button>
          <a class="btn btn-outline-secondary" href="{{ route('public.organizations') }}">Reset</a>
        </div>
      </form>

      @if(($items ?? collect())->count())
        <div class="row g-3">
          @foreach($items as $org)
            <div class="col-12 col-md-6 col-lg-4">
              <div class="card h-100">
                <div class="card-body">
                  <h6 class="mb-1">{{ $org->name ?? '—' }}</h6>
                  <div class="text-muted small mb-1">{{ $org->email ?? '' }}</div>
                  <div class="text-muted small">{{ $org->address ?? $org->location ?? '' }}</div>
                </div>
              </div>
            </div>
          @endforeach
        </div>

        <div class="mt-3">
          {{ $items->links() }}
        </div>
      @else
        <div class="text-muted">No organizations found.</div>
      @endif
    </div>
  </section>
@endsection

@push('scripts')
  {{-- page-specific JS if needed --}}
@endpush
