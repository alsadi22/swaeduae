@extends('org.layout')
@section('content')
<div class="container py-4">
  <h1 class="h5 mb-3">{{ __('Attendance Settings') }} — {{ $opportunity->title ?? ('#'.$opportunity->id) }}</h1>
  @if (session('status')) <div class="alert alert-success">{{ session('status') }}</div> @endif
  <form method="POST" action="{{ route('org.opportunities.attendance.settings.save',$opportunity) }}" class="card shadow-sm">
    @csrf
    <div class="card-body row g-3">
      <div class="col-12 col-md-4">
        <label class="form-label">{{ __('Latitude') }}</label>
        <input class="form-control" name="lat" id="lat" type="number" step="0.0000001" value="{{ old('lat',$geo['lat']) }}">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">{{ __('Longitude') }}</label>
        <input class="form-control" name="lng" id="lng" type="number" step="0.0000001" value="{{ old('lng',$geo['lng']) }}">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">{{ __('Radius (m)') }}</label>
        <input class="form-control" name="radius" id="radius" type="number" min="50" max="2000" value="{{ old('radius',$geo['radius']) }}">
      </div>
      <div class="col-12 d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" id="useHere">{{ __('Use my location') }}</button>
        <button class="btn btn-primary">{{ __('Save') }}</button>
      </div>
    </div>
  </form>
</div>
<script>
document.getElementById('useHere').addEventListener('click', ()=>{
  if (!navigator.geolocation) return alert('Geolocation not supported');
  navigator.geolocation.getCurrentPosition(pos=>{
    document.getElementById('lat').value = pos.coords.latitude.toFixed(7);
    document.getElementById('lng').value = pos.coords.longitude.toFixed(7);
    if (!document.getElementById('radius').value) document.getElementById('radius').value = 150;
  }, err=>alert('Location error: '+err.message), {enableHighAccuracy:true,timeout:8000});
});
</script>
@endsection
