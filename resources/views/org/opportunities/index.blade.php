@extends('org.layout')
@section('title', __('My Opportunities').' | '.config('app.name'))
@section('page_header')
  <x-page-header :title="__('My Opportunities')" :subtitle="__('Create and manage your volunteer opportunities')">
    <x-slot name="actions"><a class="btn btn-teal" href="{{ route('org.opportunities.create') }}">{{ __('New Opportunity') }}</a></x-slot>
  </x-page-header>
@endsection
@section('content')
  @if($list->isEmpty())
    <div class="empty-state">{{ __('No opportunities yet.') }} <a class="link-teal" href="{{ route('org.opportunities.create') }}">{{ __('Create one') }}</a></div>
  @else
    <div class="row g-3">
      @foreach($list as $o)
        <div class="col-md-6 col-lg-4">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <div class="small text-muted">{{ $o->category ?? '—' }} · {{ $o->city ?? '—' }}</div>
              <h5 class="fw-bold text-navy mb-2">{{ $o->title }}</h5>
              <div class="text-muted small mb-2">{{ optional($o->starts_at)->format('d M Y H:i') }} – {{ optional($o->ends_at)->format('d M Y H:i') }}</div>
              <a class="btn btn-outline-teal btn-sm" href="{{ route('org.opportunities.edit',$o) }}">{{ __('Edit') }}</a>
              <a class="btn btn-outline-secondary btn-sm" href="{{ route('opps.public.show',$o) }}" target="_blank">{{ __('View') }}</a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
    <div class="mt-3">{{ $list->links() }}</div>
  @endif
@endsection

@include('org.opportunities._row_cert_init')
