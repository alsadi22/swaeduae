@extends('layouts.app')

@section('title', __('Verify Certificate'))

@section('content')
<div class="container py-5">
  <div class="card shadow-sm">
    <div class="card-body">
      <h1 class="h5 mb-3">{{ __('Verify Certificate') }}</h1>

      @if($rec)
        <p class="mb-1"><strong>{{ __('Code') }}:</strong> <code>{{ $rec->code }}</code></p>
        <p class="mb-1"><strong>{{ __('Title') }}:</strong> {{ $rec->title ?? __('Certificate') }}</p>
        @if(isset($rec->issued_at))
          <p class="mb-1"><strong>{{ __('Issued') }}:</strong> {{ \Carbon\Carbon::parse($rec->issued_at)->format('M d, Y') }}</p>
        @endif
        @if(isset($rec->user_id))
          <p class="mb-1"><strong>{{ __('User ID') }}:</strong> {{ $rec->user_id }}</p>
        @endif
        @if(!empty($rec->file_path))
          <p class="mt-3">
            <a class="btn btn-outline-primary" href="{{ \Illuminate\Support\Str::startsWith($rec->file_path, ['http://','https://']) ? $rec->file_path : url($rec->file_path) }}" target="_blank">
              {{ __('Open Certificate') }}
            </a>
          </p>
        @endif
        <div class="alert alert-success mt-3 mb-0">{{ __('This certificate code is valid.') }}</div>
      @else
        <div class="alert alert-danger mb-0">{{ __('Certificate not found or code is invalid.') }}</div>
      @endif
    </div>
  </div>
</div>
@endsection
