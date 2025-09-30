#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail

A="resources/views/org/partials/apps_vs_attend.blade.php"
H="resources/views/org/partials/hours_chart.blade.php"
CSS="public/css/brand.css"

for f in "$A" "$H"; do
  [ -f "$f" ] || { echo "Missing $f"; exit 1; }
  cp "$f" "${f}.bak_$(date +%F_%H%M%S)"
done
mkdir -p "$(dirname "$CSS")"; touch "$CSS"; cp "$CSS" "${CSS}.bak_$(date +%F_%H%M%S)" || true

# ---------- apps_vs_attend (replace with tidy, idempotent content) ----------
cat > "$A" <<'BLADE'
@include('org.dashboard._safe_defaults')
@php /*__APPS_ATTEND_POLISH__*/
  $appAttend = is_array($appAttend ?? null) ? $appAttend : [];
  $aa_labels = $appAttend['labels'] ?? [];
  $aa_apps   = $appAttend['apps'] ?? [];
  $aa_attend = $appAttend['attend'] ?? [];
  $hasDataAA = (count($aa_labels) > 0) && ((int)array_sum($aa_apps) + (int)array_sum($aa_attend) > 0);
@endphp

<div class="card shadow-sm h-100">
  <div class="card-body">
    <h6 class="mb-3">{{ __('Applications vs Attendance') }}</h6>
    @if(!$hasDataAA)
      <div class="empty-chart">
        <div class="empty-chart__msg">{{ __('No data for the selected range') }}</div>
      </div>
    @else
      <canvas id="appsAttendChart"></canvas>
    @endif
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
BLADE

# ---------- hours_chart (replace with tidy, idempotent content) ----------
cat > "$H" <<'BLADE'
@include('org.dashboard._safe_defaults')
@php /*__HOURS_CHART_POLISH__*/
  $hoursChart = is_array($hoursChart ?? null) ? $hoursChart : [];
  $hc_labels  = $hoursChart['labels'] ?? ($labels ?? []);
  $hc_series  = $hoursChart['hours']  ?? ($hoursChart['series'] ?? []);
  $hc_series  = is_array($hc_series) ? $hc_series : [];
  $hasDataHC  = (count($hc_labels) > 0) && ((int)array_sum($hc_series) > 0);
@endphp

<div class="card shadow-sm h-100">
  <div class="card-body">
    <h6 class="mb-3">{{ __('Volunteer Hours') }}</h6>
    @if(!$hasDataHC)
      <div class="empty-chart">
        <div class="empty-chart__msg">{{ __('No data for the selected range') }}</div>
      </div>
    @else
      <canvas id="hoursChart"></canvas>
    @endif
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
BLADE

# ---------- CSS: empty-state style (idempotent markers) ----------
awk '
  /\/\* dashboard-empty-states:start \*\// {skip=1}
  !skip {print}
  /\/\* dashboard-empty-states:end \*\// {skip=0}
' "$CSS" > "$CSS.__tmp__" 2>/dev/null || true
mv "$CSS.__tmp__" "$CSS" 2>/dev/null || true

cat >> "$CSS" <<'CSS'
/* dashboard-empty-states:start */
.empty-chart{
  height: 240px;
  display:flex; align-items:center; justify-content:center;
  background: linear-gradient(180deg, rgba(241,245,249,.45), rgba(241,245,249,.2));
  border: 1px dashed rgba(148,163,184,.6);
  border-radius: 12px;
}
.empty-chart__msg{ color:#64748b; font-weight:600; }
/* dashboard-empty-states:end */
CSS

echo "Rebuilding Blade cache…"
php artisan view:clear >/dev/null && php artisan view:cache >/dev/null

echo "Running audit…"
php tools/audit_org_dashboard.php || true

echo "✅ Done. If you still see blank areas, hard-refresh (Ctrl/Cmd+Shift+R)."
