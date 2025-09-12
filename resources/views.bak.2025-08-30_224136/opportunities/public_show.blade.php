@extends('layouts.app')
@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="{{ url('/opportunities') }}">{{ __('Opportunities') }}</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{ $opportunity->title ?? __('Details') }}</li>
    </ol>
  </nav>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <h1 class="h4">{{ $opportunity->title ?? __('Opportunity') }}</h1>

      @if(!empty($starts_on) || !empty($ends_on))
        <p class="text-muted mb-2">
          {{ $starts_on ? __('Starts').': '.$starts_on : '' }}
          @if(!empty($starts_on) && !empty($ends_on)) &nbsp;•&nbsp; @endif
          {{ $ends_on ? __('Ends').': '.$ends_on : '' }}
        </p>
      @endif

      @if(!empty($opportunity->location))
        <p class="text-muted mb-2">{{ $opportunity->location }}</p>
      @endif

      <div class="mb-3">{!! nl2br(e($opportunity->description ?? '')) !!}</div>

      <div class="d-flex gap-2">
        @auth
          <a href="{{ url('/apply/'.$opportunity->id) }}" class="btn btn-primary btn-sm">{{ __('Apply') }}</a>
        @else
          <a href="{{ route('login',['type'=>'volunteer']) }}" class="btn btn-outline-primary btn-sm">{{ __('Login to apply') }}</a>
        @endauth
        <a href="{{ url('/opportunities') }}" class="btn btn-outline-secondary btn-sm">{{ __('Back to list') }}</a>
      </div>
    </div>
  </div>
</div>
@endsection
