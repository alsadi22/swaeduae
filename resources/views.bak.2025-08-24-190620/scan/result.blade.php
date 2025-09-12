@extends('layouts.app')
@section('title', __('Scan Result'))

@section('content')
<div class="container py-4">
  <div class="alert {{ $status==='error' ? 'alert-danger' : ($status==='expired' ? 'alert-warning' : 'alert-success') }}">
    {{ $message ?? __('Done.') }}
  </div>
  <a href="{{ url()->previous() }}" class="btn btn-outline-primary">{{ __('Back') }}</a>
</div>
@endsection
