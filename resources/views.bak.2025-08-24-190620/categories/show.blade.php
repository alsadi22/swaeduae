@extends('layouts.app')
@section('title', ucfirst($category).' | '.config('app.name'))
@section('page_header')
  <x-page-header :title="ucfirst($category)" :subtitle="__('Opportunities in this category')" />
@endsection
@section('content')
  @if($list->isEmpty())
    <div class="empty-state">{{ __('No opportunities in this category.') }}</div>
  @else
    <div class="row g-3">
      @foreach($list as $o)
        <div class="col-md-6 col-lg-4">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <div class="small text-muted">{{ $o->city ?? '—' }}</div>
              <h5 class="fw-bold text-navy mb-2">{{ $o->title }}</h5>
              <div class="text-muted small mb-2">{{ optional($o->starts_at)->format('d M Y H:i') }} – {{ optional($o->ends_at)->format('d M Y H:i') }}</div>
              <a class="btn btn-outline-teal btn-sm" href="{{ route('public.opportunities.show',$o) }}">{{ __('View') }}</a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
    <div class="mt-3">{{ $list->links() }}</div>
  @endif
@endsection
