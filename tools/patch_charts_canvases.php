<?php
$apps  = 'resources/views/org/partials/apps_vs_attend.blade.php';
$hours = 'resources/views/org/partials/hours_chart.blade.php';
$brand = 'public/css/brand.css';

function backup($f){ if (is_file($f)) copy($f, $f.'.bak_'.date('Y-m-d_His')); }

function ensureCanvasAndScript($file, $titleRegex, $canvasId, $scriptNowdoc) {
  if (!is_file($file)) { echo "Missing $file\n"; return; }
  backup($file);
  $src = file_get_contents($file);

  // 1) Ensure a canvas with the desired id exists
  if (strpos($src, 'id="'.$canvasId.'"') === false) {
    // try to insert right after the title <h6>...</h6>
    $insert = "\n    <div class=\"chart-box\"><canvas id=\"{$canvasId}\"></canvas></div>\n";
    $pattern = '#(<h6[^>]*>'.$titleRegex.'</h6>)(.*?\R)#i';
    if (preg_match($pattern, $src)) {
      $src = preg_replace($pattern, '$1$2'.$insert, $src, 1);
    } else {
      // fallback: append a minimal card at the end
      $card = <<<BLADE

<div class="card shadow-sm h-100">
  <div class="card-body">
    <h6 class="mb-3">{{ __("$titleRegex") }}</h6>
    <div class="chart-box"><canvas id="$canvasId"></canvas></div>
  </div>
</div>

BLADE;
      $src .= "\n".$card;
    }
  }

  // 2) Ensure a script block initializes the chart (idempotent)
  if (strpos($src, "@push('scripts')") === false || strpos($src, $canvasId) === false) {
    // Append our push block only if it is not already there for this canvas id
    if (strpos($src, $canvasId) === false || stripos($src, 'new Chart(') === false) {
      $src .= "\n".$scriptNowdoc."\n";
    }
  }

  file_put_contents($file, $src);
  echo "Patched: $file\n";
}

// apps_vs_attend
$appsScript = <<<'BLADE'
@push('scripts')
<script>
(function () {
  if (!window.Chart) return;
  var el = document.getElementById('appsAttendChart');
  if (!el) return;
  // Normalize incoming data
  var src   = (window.appAttend || {}); // allow a global if you later expose one
  var labels = (src.labels || @json(($appAttend['labels'] ?? [])));
  var apps   = (src.apps   || @json(($appAttend['apps'] ?? [])));
  var attend = (src.attend || @json(($appAttend['attend'] ?? [])));
  labels = Array.isArray(labels) ? labels : [];
  apps   = Array.isArray(apps)   ? apps   : [];
  attend = Array.isArray(attend) ? attend : [];
  var len = Math.max(labels.length, apps.length, attend.length);
  while (labels.length < len) labels.push('');
  while (apps.length   < len) apps.push(0);
  while (attend.length < len) attend.push(0);

  new Chart(el, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [
        { label: 'Applications', data: apps },
        { label: 'Attendance',  data: attend }
      ]
    },
    options: { responsive: true, maintainAspectRatio: false }
  });
})();
</script>
@endpush
BLADE;

ensureCanvasAndScript(
  $apps,
  '(Applications\s+vs\s+Attendance)',
  'appsAttendChart',
  $appsScript
);

// hours_chart
$hoursScript = <<<'BLADE'
@push('scripts')
<script>
(function () {
  if (!window.Chart) return;
  var el = document.getElementById('hoursChart');
  if (!el) return;

  // Accept several shapes from the backend safely
  var labels = @json(($hoursChart['labels'] ?? ($labels ?? [])));
  var values = @json(($hoursChart['values'] ?? ($hoursChart['data'] ?? [])));
  labels = Array.isArray(labels) ? labels : [];
  values = Array.isArray(values) ? values : [];
  var len = Math.max(labels.length, values.length);
  while (labels.length < len) labels.push('');
  while (values.length < len) values.push(0);

  new Chart(el, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{ label: 'Hours', data: values, fill: true, tension: 0.35 }]
    },
    options: { responsive: true, maintainAspectRatio: false }
  });
})();
</script>
@endpush
BLADE;

ensureCanvasAndScript(
  $hours,
  '(Volunteer\s+Hours)',
  'hoursChart',
  $hoursScript
);

// CSS helper for chart height
if (!is_dir(dirname($brand))) mkdir(dirname($brand), 0775, true);
$css = is_file($brand) ? file_get_contents($brand) : '';
if (strpos($css, '.chart-box') === false) {
  $css .= "\n.chart-box{height:300px;position:relative;}\n";
  file_put_contents($brand, $css);
  echo "Polished CSS: $brand\n";
}

echo "Done.\n";
