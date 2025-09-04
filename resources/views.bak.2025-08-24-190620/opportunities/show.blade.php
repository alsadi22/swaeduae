@extends('layouts.app')

@section('title', $opportunity->title ?? __('Opportunity'))
@section('content')
@include('partials.page_header', ['title' => $opportunity->title ?? __('Opportunity')])

<div class="container my-4">
  <div class="row g-4">
    <div class="col-md-8">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          @if(!empty($opportunity->description))
            <div class="mb-3">{!! nl2br(e($opportunity->description)) !!}</div>
          @endif

          <dl class="row">
            @if(!empty($opportunity->region))
              <dt class="col-sm-3">{{ __('Region') }}</dt>
              <dd class="col-sm-9">{{ $opportunity->region }}</dd>
            @endif
            @if(!empty($opportunity->category))
              <dt class="col-sm-3">{{ __('Category') }}</dt>
              <dd class="col-sm-9">{{ $opportunity->category }}</dd>
            @endif
            @if(!empty($opportunity->start_date))
              <dt class="col-sm-3">{{ __('Start') }}</dt>
              <dd class="col-sm-9">{{ \Illuminate\Support\Carbon::parse($opportunity->start_date)->toFormattedDateString() }}</dd>
            @endif
            @if(!empty($opportunity->end_date))
              <dt class="col-sm-3">{{ __('End') }}</dt>
              <dd class="col-sm-9">{{ \Illuminate\Support\Carbon::parse($opportunity->end_date)->toFormattedDateString() }}</dd>
            @endif
            @if(!empty($opportunity->deadline))
              <dt class="col-sm-3">{{ __('Apply by') }}</dt>
              <dd class="col-sm-9">{{ \Illuminate\Support\Carbon::parse($opportunity->deadline)->toFormattedDateString() }}</dd>
            @endif
          </dl>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          @include('partials.apply_button', ['type' => 'opportunity', 'recordId' => $opportunity->id])
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
