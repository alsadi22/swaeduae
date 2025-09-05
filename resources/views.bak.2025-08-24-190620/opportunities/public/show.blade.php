@extends('layouts.app')
@section('title', ($o->title ?? $o->name ?? 'Opportunity') . ' | ' . config('app.name'))
@section('content')
<div class="container my-4">
  <a href="{{ route('opportunities.index') }}" class="btn btn-sm btn-outline-secondary mb-3">Back to list</a>
  <h1 class="h4 mb-2">{{ $o->title ?? $o->name ?? ('Opportunity #'.$o->id) }}</h1>
  <div class="text-muted mb-3">
    {{ trim(($o->city ?? '').' '.(($o->region ?? $o->emirate ?? '') ? '· '.($o->region ?? $o->emirate) : '')) }}
  </div>
  <div>{!! nl2br(e($o->description ?? $o->details ?? '')) !!}</div>
</div>
@endsection
