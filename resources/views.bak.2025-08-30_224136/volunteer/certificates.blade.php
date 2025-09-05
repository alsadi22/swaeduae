@extends('layouts.app')
@section('title', __('My Certificates'))
@section('content')
@include('partials.page_header', ['title' => __('My Certificates')])

<div class="container my-4">
  @if($certificates->count() === 0)
    <div class="alert alert-info">{{ __('No certificates yet.') }}</div>
  @endif

  <div class="row g-3">
    @foreach($certificates as $cert)
      <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-body">
            <h6 class="mb-1">
              {{ $cert->title ?? __('Volunteer Certificate') }}
            </h6>
            @if($cert->issued_at)
              <div class="text-muted small mb-2">{{ __('Issued') }}: {{ $cert->issued_at->format('Y-m-d') }}</div>
            @endif
            @if($cert->event)
              <div class="small mb-2">{{ __('Event') }}: {{ $cert->event->title ?? __('(untitled)') }}</div>
            @endif
            <div class="d-flex gap-2">
              @if(!empty($cert->pdf_path))
                <a class="btn btn-sm btn-primary" href="{{ \Illuminate\Support\Facades\Storage::url($cert->pdf_path) }}" target="_blank">{{ __('View PDF') }}</a>
              @endif
              @if(!empty($cert->code))
                <a class="btn btn-sm btn-outline-secondary" href="{{ url('/verify/'.$cert->code) }}" target="_blank">{{ __('Verify') }}</a>
              @endif
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <div class="mt-3">{{ $certificates->links() }}</div>
</div>
@endsection
