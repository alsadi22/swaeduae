@extends('layouts.app')
@section('title', __('My Applications'))
@section('content')
<div class="container py-4">
  <h1 class="mb-3">{{ __('My Applications') }}</h1>

  @if(($rows->total() ?? 0) === 0)
    <div class="alert alert-info">{{ __('No applications yet.') }}</div>
  @else
  <div class="list-group">
    @foreach($rows as $a)
      <div class="list-group-item d-flex justify-content-between">
        <div>
          <div><strong>{{ $a->opportunity_title ?? ('#'.$a->opportunity_id) }}</strong></div>
          @if(!empty($a->status))<div class="small text-muted">{{ __('Status') }}: {{ $a->status }}</div>@endif
        </div>
        <div class="text-muted small">{{ $a->created_at }}</div>
      </div>
    @endforeach
  </div>
  <div class="mt-3">{{ $rows->links() }}</div>
  @endif
</div>
@endsection
