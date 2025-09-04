@extends('layouts.app')
@section('title', __('QR Attendance'))

@section('content')
<div class="container py-5" style="max-width:720px">
  <h3 class="mb-4">{{ __('QR Attendance') }}</h3>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body">
      <form id="scan-form" method="POST" action="{{ route('scan.checkout') }}" class="mb-3">
        @csrf
        <div class="mb-3">
          <label class="form-label">{{ __('Code (optional)') }}</label>
          <input class="form-control" name="code" placeholder="">
          <div class="form-text">{{ __('If your QR encodes a code, paste it here; otherwise leave empty.') }}</div>
        </div>

        <div class="mb-3">
          <label class="form-label">{{ __('Opportunity ID (optional)') }}</label>
          <input class="form-control" name="opportunity_id" placeholder="">
        </div>

        <input type="hidden" name="lat" id="scan-lat">
        <input type="hidden" name="lng" id="scan-lng">
        <div class="small text-muted mb-3" id="geo-status">{{ __('Location not shared (Geolocation has been disabled in this document by permissions policy.)') }}</div>

        <div class="d-flex gap-2">
          <button class="btn btn-outline-secondary" formaction="{{ route('scan.checkout') }}">{{ __('Check out') }}</button>
          <button class="btn btn-primary" formaction="{{ route('scan.checkin') }}">{{ __('Check in') }}</button>
        </div>
      </form>

      <div><a href="{{ url('/') }}" class="btn btn-link px-0">{{ __('Back to home') }}</a></div>
    </div>
  </div>
</div>

<script>
(function(){
  if (!navigator.geolocation) return;
  const status = document.getElementById('geo-status');
  navigator.permissions && navigator.permissions.query({name:'geolocation'}).then(function(p){
    // Update hint based on permission
  }).catch(function(){});

  navigator.geolocation.getCurrentPosition(function(pos){
    document.getElementById('scan-lat').value = pos.coords.latitude.toFixed(7);
    document.getElementById('scan-lng').value = pos.coords.longitude.toFixed(7);
    if (status) status.textContent = 'Location will be captured if permitted.';
  }, function(){
    if (status) status.textContent = 'Location not shared (permission denied).';
  }, {enableHighAccuracy:true, maximumAge:15000, timeout:8000});
})();
</script>
@endsection
