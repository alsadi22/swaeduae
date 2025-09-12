@extends('layouts.app')
@section('title', __('Volunteer Resume'))

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white rounded shadow mt-10">
    <h1 class="text-2xl font-bold mb-6">{{ __('Volunteer Resume') }}</h1>
    <p>{{ __('Name:') }} {{ $user->name }}</p>
    <p>{{ __('Email:') }} {{ $user->email }}</p>
    <h2 class="font-bold mt-4 mb-2">{{ __('Events Participated') }}</h2>
    <ul class="list-disc pl-6">
        @foreach($events as $event)
            <li>{{ $event->title }} ({{ \Carbon\Carbon::parse($event->date)->format('d M Y') }})</li>
        @endforeach
    </ul>
</div>
@endsection
