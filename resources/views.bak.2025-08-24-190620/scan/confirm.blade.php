@extends('layouts.app')
@section('title', __('Confirm Scan'))

@section('content')
<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <h4 class="mb-2">{{ __('Confirm ') }}{{ $direction === 'out' ? __('Check-out') : __('Check-in') }}</h4>
      <p class="text-muted">{{ __('Please allow location to verify you are at the event, then we will submit automatically.') }}</p>

      <form id="scanForm" method="POST" action="{{ route('scan') }}">
        @csrf
        <input type="hidden" name="t" value="{{ $token }}">
        <input type="hidden" name="lat" id="lat">
        <input type="hidden" name="lng" id="lng">
        <div id="status" class="small text-muted">{{ __('Requesting location…') }}</div>
      </form>
    </div>
  </div>
</div>

<script>
(function(){
  const form = document.getElementById('scanForm');
  const status = document.getElementById('status');
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(pos){
      document.getElementById('lat').value = pos.coords.latitude;
      document.getElementById('lng').value = pos.coords.longitude;
      status.textContent = '{{ __('Location captured — submitting…') }}';
      setTimeout(()=>form.submit(), 300);
    }, function(err){
      status.textContent = '{{ __('Location unavailable — submitting without GPS…') }}';
      setTimeout(()=>form.submit(), 300);
    }, { enableHighAccuracy:true, timeout:5000, maximumAge:0 });
  } else {
    status.textContent = '{{ __('Geolocation not supported — submitting…') }}';
    setTimeout(()=>form.submit(), 300);
  }
})();
</script>
@endsection
