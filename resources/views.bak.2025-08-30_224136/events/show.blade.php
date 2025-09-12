@extends('layouts.app')

@section('title', $event->title ?? __('Event'))

@section('content')
<section id="main" tabindex="-1" class="container py-4">
  <a href="{{ route('events.index') }}">&larr; {{ __('Back to events') }}</a>
  <h1 class="mb-2">{{ $event->title ?? __('Event') }}</h1>
  @if(!empty($event->date))
    <p><strong>{{ __('Date') }}:</strong> {{ \Illuminate\Support\Carbon::parse($event->date)->toFormattedDateString() }}</p>
  @endif
  @if(!empty($event->location))
    <p><strong>{{ __('Location') }}:</strong> {{ $event->location }}</p>
  @endif
  @if(!empty($event->description))
    <article class="prose">{!! nl2br(e($event->description)) !!}</article>
  @endif
</section>
@endsection
