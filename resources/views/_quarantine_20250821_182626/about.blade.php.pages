@php $seo = ["title"=>"About SawaedUAE","description"=>"About our mission and platform"]; @endphp
@extends('layouts.app')

@section('title', __('About Us'))
@section('content')
@include('partials.page_header', ['title' => __('About SawaedUAE'), 'subtitle' => __('Connecting volunteers with meaningful opportunities across the UAE.')])

<div class="container my-4">
  <div class="row g-4">
    <div class="col-md-8">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <h5 class="card-title mb-3">{{ __('Our Mission') }}</h5>
          <p class="mb-0">{{ __('We help organizations post opportunities and make it easy for volunteers to discover, apply, track hours, and earn verified certificates.') }}</p>
        </div>
      </div>
      <div class="card shadow-sm border-0 mt-3">
        <div class="card-body">
          <h5 class="card-title mb-3">{{ __('What We Offer') }}</h5>
          <ul class="mb-0">
            <li>{{ __('Browse and filter volunteer opportunities') }}</li>
            <li>{{ __('QR-based attendance and accurate hours tracking') }}</li>
            <li>{{ __('Auto-generated certificates with QR verification') }}</li>
            <li>{{ __('Admin & organization dashboards with exports') }}</li>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <h6 class="mb-2">{{ __('Contact') }}</h6>
          <p class="mb-2">{{ __('Questions or partnerships?') }}</p>
          <a href="{{ Route::has('contact.show') ? route('contact.show') : url('/contact') }}" class="btn btn-primary w-100">{{ __('Get in touch') }}</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
