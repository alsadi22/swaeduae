@extends('layouts.app')
@section('title', __('My Certificates'))
@section('content')
<div class="container py-4">
  <h1 class="mb-3">{{ __('My Certificates') }}</h1>
  @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
  @if(($rows->total() ?? 0) === 0)
    <div class="alert alert-info">{{ __('No certificates yet.') }}</div>
  @else
  <div class="list-group">
    @foreach($rows as $c)
      <div class="list-group-item d-flex justify-content-between align-items-center">
        <div>
          <div><strong>{{ $c->code }}</strong></div>
          <div class="text-muted small">{{ $c->title ?? 'Certificate' }}</div>
        </div>
        <div class="d-flex gap-2">
          <a class="btn btn-sm btn-outline-primary" href="{{ url('/verify/'.$c->code) }}" target="_blank">{{ __('Verify') }}</a>
          <a class="btn btn-sm btn-primary" href="{{ route('my.certificates.download',$c->id) }}">{{ __('Download') }}</a>
        </div>
      </div>
    @endforeach
  </div>
  <div class="mt-3">{{ $rows->links() }}</div>
  @endif
</div>
@endsection
