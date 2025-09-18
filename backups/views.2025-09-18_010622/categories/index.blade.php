@extends('layouts.app')
@section('title', __('Categories').' | '.config('app.name'))

@section('page_header')
  <x-page-header :title="__('Categories')" :subtitle="__('Browse by category')" />
@endsection

@section('content')
  @php($cats = ($categories ?? collect())->filter()->unique()->values())
  @if($cats->isEmpty() && class_exists(\App\Models\Opportunity::class))
  @endif

  @if($cats->count())
    <div class="row g-3">
      @foreach($cats as $cat)
        <div class="col-6 col-md-4 col-lg-3">
          <a class="card p-3 hover-shadow text-decoration-none"
             href="{{ \Illuminate\Support\Facades\Route::has('public.opportunities') ? \Illuminate\Support\Facades\Route::has('public.opportunities') ? route('opportunities.index') : url('/opportunities') : url('/opportunities') }}?category={{ urlencode($cat) }}">
            <div class="fw-semibold">{{ $cat }}</div>
            <div class="text-muted small">{{ __('View opportunities') }}</div>
          </a>
        </div>
      @endforeach
    </div>
  @else
    <div class="empty-state">{{ __('No categories found.') }}</div>
  @endif
@endsection
