@extends('layouts.app')
@section('title', __('My Volunteer Profile'))

@section('content')
<div class="max-w-3xl mx-auto bg-white p-8 mt-10 rounded shadow">
    <h1 class="text-2xl font-bold mb-6">{{ __('My Profile') }}</h1>

    <div class="mb-4">
        <strong>{{ __('Name:') }}</strong> {{ $user->name }}<br>
        <strong>{{ __('Email:') }}</strong> {{ $user->email }}<br>
        <strong>{{ __('Volunteer Hours:') }}</strong> {{ $hours }}<br>
        <strong>{{ __('Events Participated:') }}</strong> {{ $events_count }}
    </div>

    <div class="mb-6">
        <h2 class="font-bold mb-2">{{ __('Recent Events') }}</h2>
        <ul class="list-disc pl-6">
            @foreach($recent_events as $event)
                <li>{{ $event->title }} ({{ \Carbon\Carbon::parse($event->date)->format('d M Y') }})</li>
            @endforeach
        </ul>
    </div>

    <div class="mb-6">
        <h2 class="font-bold mb-2">{{ __('Certificates') }}</h2>
        <ul>
            @foreach($certificates as $cert)
                <li>
                    {{ $cert->certificate_code }} - {{ ucfirst($cert->status) }} 
                    <a href="{{ route('volunteer.generateCertificate', $cert->id) }}" target="_blank" class="text-blue-700 underline">{{ __('View') }}</a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="mb-6">
        <h2 class="font-bold mb-2">{{ __('KYC Document') }}</h2>
        @if($kyc)
            <p>{{ __('Status:') }} {{ ucfirst($kyc->status) }}</p>
            <a href="{{ Storage::url($kyc->document_path) }}" target="_blank" class="text-blue-700 underline">{{ __('View Document') }}</a>
        @else
            <p>{{ __('No KYC document uploaded.') }}</p>
        @endif
        <form action="{{ route('volunteer.uploadKyc') }}" method="POST" enctype="multipart/form-data" class="mt-4">
            @csrf
            <input type="file" name="kyc_document" accept=".pdf,.jpg,.jpeg,.png" required>
            <button type="submit" class="ml-4 bg-blue-700 text-white px-4 py-2 rounded">{{ __('Upload KYC') }}</button>
        </form>
    </div>
</div>
@endsection
