@extends('org.layout')
@section('title', __('My Opportunities').' | '.config('app.name'))

@section('page_header')
  <x-page-header :title="__('My Opportunities')" :subtitle="__('Manage your organization posts')" />
@endsection

@section('content')
  @include('partials.flash')

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h5 m-0">{{ __('Opportunities') }}</h2>
    <a href="{{ route('org.opps.create') }}" class="btn btn-teal">
      <i class="bi bi-plus-circle me-1"></i> {{ __('Create') }}
    </a>
  </div>

  @php($items = $opps ?? ($opportunities ?? collect()))
  @if($items->count())
    <div class="row g-3">
      @foreach($items as $op)
        <div class="col-12 col-md-6 col-lg-4">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title">{{ $op->title }}</h5>
              <div class="text-muted small mb-2">
                @if($op->category) <span class="me-2">#{{ $op->category }}</span>@endif
                @if($op->city) <span class="me-2"><i class="bi bi-geo-alt"></i> {{ $op->city }}</span>@endif
              </div>
              <p class="card-text line-clamp-3">{{ \Illuminate\Support\Str::limit($op->description, 140) }}</p>
            </div>
            <div class="card-footer d-flex gap-2">
              <a href="{{ route('org.opps.edit', $op) }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-pencil"></i> {{ __('Edit') }}
              </a>
              <form method="POST" action="{{ route('org.opps.destroy', $op) }}"
                    onsubmit="return confirm('{{ __('Delete this opportunity?') }}')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">
                  <i class="bi bi-trash"></i> {{ __('Delete') }}
                </button>
              </form>
              <a href="{{ route('public.opportunities.show', $op) }}" class="btn btn-sm btn-light ms-auto">
                {{ __('View public') }}
              </a>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    @if(method_exists($items, 'links'))
      <div class="mt-3">{{ $items->links() }}</div>
    @endif
  @else
    <div class="empty-state">
      <p class="mb-3">{{ __('No opportunities yet.') }}</p>
      <a href="{{ route('org.opps.create') }}" class="btn btn-teal">{{ __('Create your first opportunity') }}</a>
    </div>
  @endif
@endsection
