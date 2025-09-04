@include('org.dashboard._safe_defaults')
@php /*__HOURS_CHART_CANVAS_ALWAYS__*/
  $hoursChart = is_array($hoursChart ?? null) ? $hoursChart : [];
  $hc_labels  = $hoursChart['labels'] ?? ($labels ?? []);
  $hc_series  = $hoursChart['hours']  ?? ($hoursChart['series'] ?? []);
  $hc_series  = is_array($hc_series) ? $hc_series : [];
  $hasDataHC  = (count($hc_labels) > 0) && ((int)array_sum($hc_series) > 0);
@endphp

<div class="card shadow-sm h-100">
  <div class="card-body">
    <h6 class="mb-3">{{ __('Volunteer Hours') }}</h6>
    <div class="chart-wrap">
      <canvas id="hoursChart" aria-label="Volunteer Hours"></canvas>
      @unless($hasDataHC)
        <div class="empty-chart"><div class="empty-chart__msg">{{ __('No data for the selected range') }}</div></div>
      @endunless
    </div>
  </div>
</div>

@push('scripts')
@if($hasDataHC)
<script>
(function(){
  const el = document.getElementById('hoursChart');
  if(!el || !window.Chart) return;
  const labels = @json($hc_labels);
  const data   = @json($hc_series);
  new Chart(el.getContext('2d'), {
    type: 'line',
    data: { labels, datasets: [
      { label: 'Hours', data, fill: true, tension: .35,
        backgroundColor: 'rgba(59,130,246,.15)', borderColor: 'rgba(59,130,246,1)', pointRadius: 0 }
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
