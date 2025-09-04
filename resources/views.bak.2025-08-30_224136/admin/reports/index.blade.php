@extends('admin.layout')
@section('title', __('Reports'))
@section('content')
@include('admin.partials.nav')

<div class="row g-3 mb-3">
  <div class="col-sm-6 col-lg-3"><div class="card"><div class="card-body"><div class="text-muted small">{{ __('Users') }}</div><div class="h4">{{ $counts['users'] }}</div></div></div></div>
  <div class="col-sm-6 col-lg-3"><div class="card"><div class="card-body"><div class="text-muted small">{{ __('Opportunities') }}</div><div class="h4">{{ $counts['opportunities'] }}</div></div></div></div>
  <div class="col-sm-6 col-lg-3"><div class="card"><div class="card-body"><div class="text-muted small">{{ __('Applications') }}</div><div class="h4">{{ $counts['applications'] }}</div></div></div></div>
</div>

<div class="card mb-3"><div class="card-body">
  <h5 class="mb-3">{{ __('Users per Month') }}</h5>
  <canvas id="usersChart" height="110"></canvas>
</div></div>

@if($hours && count($hours))
<div class="card"><div class="card-body">
  <h5 class="mb-3">{{ __('Volunteer Hours per Month') }}</h5>
  <canvas id="hoursChart" height="110"></canvas>
</div></div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const mu = @json(array_values($monthlyUsers));
const lu = @json(array_keys($monthlyUsers));
new Chart(document.getElementById('usersChart'), {
  type: 'line',
  data: { labels: lu, datasets: [{ label: 'Users', data: mu }] },
});

@if($hours && count($hours))
const mh = @json(array_values($hours));
const lh = @json(array_keys($hours));
new Chart(document.getElementById('hoursChart'), {
  type: 'line',
  data: { labels: lh, datasets: [{ label: 'Hours', data: mh }] },
});
@endif
</script>
@endpush
@endsection
