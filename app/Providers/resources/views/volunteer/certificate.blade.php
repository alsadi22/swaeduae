@extends('layouts.app')
@section('title', __('Certificate'))

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white rounded shadow mt-10 text-center">
    <h1 class="text-3xl font-bold mb-6">{{ __('Certificate of Participation') }}</h1>
    <p>{{ __('Certificate Code:') }} {{ $certificate->certificate_code }}</p>
    <p>{{ __('Status:') }} {{ ucfirst($certificate->status) }}</p>
    <p>{{ __('Issued At:') }} {{ $certificate->issued_at ?? __('N/A') }}</p>
    <p class="mt-6 italic">{{ __('This is a placeholder certificate view. Full PDF generation coming soon.') }}</p>
</div>
@endsection
