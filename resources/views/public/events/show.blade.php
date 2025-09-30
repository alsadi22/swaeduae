@extends('public.layout')

@section('title', 'Show')
@section('content')
<div class="container py-4">
  <h1>{{ $event->title ?? __('Event') }}</h1>
  <p>{{ $event->description ?? __('No description provided.') }}</p>
  <p><strong>{{ __('Starts:') }}</strong> {{ $event->start_date ?? '—' }}</p>
  <p><strong>{{ __('Ends:') }}</strong> {{ $event->end_date ?? '—' }}</p>
</div>
@endsection
