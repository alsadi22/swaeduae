@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1>{{ $opportunity->title ?? __('Opportunity') }}</h1>
  <p>{{ $opportunity->description ?? __('No description provided.') }}</p>
  <p><strong>{{ __('Starts:') }}</strong> {{ $opportunity->start_date ?? '—' }}</p>
  <p><strong>{{ __('Ends:') }}</strong> {{ $opportunity->end_date ?? '—' }}</p>
</div>
@endsection
