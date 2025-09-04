@extends("org.layout")
@include('org.dashboard._safe_defaults')
@php /*__APPS_ATTEND_CANVAS_ALWAYS__*/
  $appAttend = is_array($appAttend ?? null) ? $appAttend : [];
  $aa_labels = $appAttend['labels'] ?? [];
  $aa_apps   = $appAttend['apps']   ?? [];
  $aa_attend = $appAttend['attend'] ?? [];
  $hasDataAA = (count($aa_labels) > 0) && ((int)array_sum($aa_apps) + (int)array_sum($aa_attend) > 0);
@endphp

<div class="card shadow-sm h-100">
  <div class="card-body">
    <h6 class="mb-3">{{ __('Applications vs Attendance') }}</h6>
    <div class="chart-wrap">
      <canvas id="appsAttendChart" aria-label="Applications vs Attendance"></canvas>
      @unless($hasDataAA)
        <div class="empty-chart"><div class="empty-chart__msg">{{ __('No data for the selected range') }}</div></div>
      @endunless
    </div>
  </div>
</div>

@push('scripts')
@if($hasDataAA)
<script>
(function(){
  const el = document.getElementById('appsAttendChart');
  if(!el || !window.Chart) return;
  const labels = @json($aa_labels);
  const apps   = @json($aa_apps);
  const attend = @json($aa_attend);
  new Chart(el.getContext('2d'), {
    type: 'bar',
    data: { labels, datasets: [
      { label: 'Applications', data: apps,   backgroundColor: 'rgba(59,130,246,.6)' },
      { label: 'Attendance',  data: attend, backgroundColor: 'rgba(16,185,129,.6)' }
    ]},
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true } }
    }
  });
})();
</script>
@endif
@endpush
