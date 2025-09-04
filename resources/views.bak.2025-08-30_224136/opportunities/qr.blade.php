@extends('layouts.app')
@section('title', __('Event QR Check-in/Out').' | '.config('app.name'))

@section('page_header')
  <x-page-header :title="__('QR Codes for')" :subtitle="$opportunity->title" />
@endsection

@section('content')
  <div class="row g-3">
    <div class="col-md-6">
      <div class="card shadow-sm h-100 text-center">
        <div class="card-body">
          <h5 class="fw-bold text-navy mb-3">{{ __('Check-in') }}</h5>
          @php $inUrl = route('attendance.checkin',['opp'=>$opportunity->id,'token'=>$opportunity->checkin_token]); @endphp
          <img alt="Check-in QR" class="img-fluid" src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&data={{ urlencode($inUrl) }}">
          <div class="mt-2 small"><a class="link-teal" href="{{ $inUrl }}" target="_blank">{{ $inUrl }}</a></div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card shadow-sm h-100 text-center">
        <div class="card-body">
          <h5 class="fw-bold text-navy mb-3">{{ __('Check-out') }}</h5>
          @php $outUrl = route('attendance.checkout',['opp'=>$opportunity->id,'token'=>$opportunity->checkout_token]); @endphp
          <img alt="Check-out QR" class="img-fluid" src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&data={{ urlencode($outUrl) }}">
          <div class="mt-2 small"><a class="link-teal" href="{{ $outUrl }}" target="_blank">{{ $outUrl }}</a></div>
        </div>
      </div>
    </div>
  </div>

  <div class="alert alert-light border mt-3">
    <i class="bi bi-info-circle me-1"></i>
    {{ __('Volunteers scan the QR and login. Times are recorded and hours are auto-calculated on checkout.') }}
  </div>
@endsection
