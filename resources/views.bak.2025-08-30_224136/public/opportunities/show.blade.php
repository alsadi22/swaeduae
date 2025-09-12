@extends('layouts.travelpro')

@section('content')
@php
  $fake = [
    1 => ['title'=>__('Beach Cleanup'),'city'=>'Dubai','date'=>'2025-09-05','hours'=>4,'desc'=>__('Help clean the beach and protect marine life.')],
    2 => ['title'=>__('Teach Coding to Kids'),'city'=>'Abu Dhabi','date'=>'2025-09-12','hours'=>2,'desc'=>__('Mentor children in basic programming logic.')],
    3 => ['title'=>__('Tree Planting Day'),'city'=>'Sharjah','date'=>'2025-09-20','hours'=>3,'desc'=>__('Join us in planting trees in public parks.')],
  ];
  $id = (int) (request()->route('id') ?? 0);
  $op = $fake[$id] ?? [
    'title' => __('Opportunity #').$id,
    'city'  => '',
    'date'  => '',
    'hours' => 0,
    'desc'  => __('Details coming soon.')
  ];
@endphp

<section class="py-5">
  <div class="container">
    <a href="{{ route('opportunities.index') }}" class="btn btn-link px-0 mb-3">&larr; {{ __('Back to opportunities') }}</a>
    <h1 class="mb-2">{{ $op['title'] }}</h1>
    <p class="text-muted">
      {{ $op['city'] }}
      @if(!empty($op['date'])) • {{ \Carbon\Carbon::parse($op['date'])->toFormattedDateString() }} @endif
      • {{ $op['hours'] }} {{ __('hours') }}
    </p>
    <p class="lead">{{ $op['desc'] }}</p>
    <a class="btn btn-primary" href="{{ url('/register') }}">{{ __('Apply now') }}</a>
  </div>
</section>
@endsection
