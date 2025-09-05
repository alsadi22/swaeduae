@extends('layouts.app')
@section('title', __('My Hours'))
@section('content')
<div class="container py-4">
  <h1 class="mb-3">{{ __('My Hours') }}</h1>
  <div class="mb-2"><strong>{{ __('Total Minutes') }}:</strong> {{ (int)($total ?? 0) }}</div>

  @if(($rows->total() ?? 0) === 0)
    <div class="alert alert-info">{{ __('No hours yet.') }}</div>
  @else
  <div class="list-group">
    @foreach($rows as $r)
      <div class="list-group-item">
        <div class="d-flex justify-content-between">
          <div><strong>{{ $r->opportunity_title ?? ('#'.$r->opportunity_id) }}</strong></div>
          <div>{{ (int)($r->minutes ?? 0) }} {{ __('min') }}</div>
        </div>
        @if(!empty($r->notes))<div class="small text-muted">{{ $r->notes }}</div>@endif
      </div>
    @endforeach
  </div>
  <div class="mt-3">{{ $rows->links() }}</div>
  @endif
</div>
@endsection
