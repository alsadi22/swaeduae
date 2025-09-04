@extends('layouts.app')
@section('content')
<div class="container py-5">
  <div class="card shadow-sm">
    <div class="card-body">
      <h1 class="h4 mb-3">{{ __('Certificate Verification') }}</h1>
      @if(!$ok)
        <div class="alert alert-danger">{{ __('No certificate found for code') }}: <strong>{{ $code }}</strong></div>
      @else
        <div class="alert alert-success">{{ __('Valid certificate') }}</div>
        <ul class="list-unstyled">
          <li><strong>{{ __('Code') }}:</strong> {{ $code }}</li>
          <li><strong>{{ __('Name') }}:</strong> {{ $name }}</li>
          <li><strong>{{ __('Opportunity') }}:</strong> {{ $opportunity }}</li>
          @if($hours!==null)<li><strong>{{ __('Hours') }}:</strong> {{ $hours }}</li>@endif
          @if($file)<li><strong>{{ __('File') }}:</strong> <a href="{{ $file }}" target="_blank">{{ __('Download') }}</a></li>@endif
        </ul>
      @endif
    </div>
  </div>
</div>
@endsection
