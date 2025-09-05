@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-6">{{ __('messages.dashboard') }}</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="dashboard-card bg-blue-50 p-6 rounded text-center shadow">
            <i class="bi bi-person-circle text-blue-700 text-3xl"></i>
            <div class="text-lg mt-2">{{ __('messages.certificates') }}</div>
            <div class="text-2xl font-bold">{{ $certCount ?? 0 }}</div>
        </div>
        <div class="dashboard-card bg-green-50 p-6 rounded text-center shadow">
            <i class="bi bi-clock text-green-700 text-3xl"></i>
            <div class="text-lg mt-2">{{ __('messages.volunteer_hours') }}</div>
            <div class="text-2xl font-bold">{{ $hours ?? 0 }}</div>
        </div>
        <div class="dashboard-card bg-yellow-50 p-6 rounded text-center shadow">
            <i class="bi bi-calendar-event text-yellow-700 text-3xl"></i>
            <div class="text-lg mt-2">{{ __('messages.events_attended') }}</div>
            <div class="text-2xl font-bold">{{ $eventsAttended ?? 0 }}</div>
        </div>
    </div>
    <div class="bg-white p-6 rounded shadow">
        <h2 class="font-bold mb-4">{{ __('messages.recent_opportunities') }}</h2>
        @if(isset($opportunities) && count($opportunities))
            <ul>
                @foreach($opportunities as $opportunity)
                    <li>{{ $opportunity->title }}</li>
                @endforeach
            </ul>
        @else
            <div>{{ __('messages.no_opportunities') }}</div>
        @endif
    </div>
</div>
@endsection
