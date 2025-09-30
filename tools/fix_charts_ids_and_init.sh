PHP_BIN=${PHP_BIN:-php}
#!/usr/bin/env bash
set -euo pipefail

APPS="resources/views/org/partials/apps_vs_attend.blade.php"
HOURS="resources/views/org/partials/hours_chart.blade.php"
BRAND="public/css/brand.css"

[ -f "$APPS" ]  || { echo "Missing $APPS";  exit 1; }
[ -f "$HOURS" ] || { echo "Missing $HOURS"; exit 1; }

cp "$APPS"  "${APPS}.bak_$(date +%F_%H%M%S)"
cp "$HOURS" "${HOURS}.bak_$(date +%F_%H%M%S)"

# --- apps_vs_attend: ensure canvas + script ---
php -r '
$part = getenv("APPS");
$src  = file_get_contents($part);

if (strpos($src, "id=\"appsAttendChart\"") === false) {
  $block = <<<'BLADE'

<div class="card shadow-sm h-100">
  <div class="card-body">
    <h6 class="mb-3">{{ __("Applications vs Attendance") }}</h6>
    <div class="chart-box"><canvas id="appsAttendChart"></canvas></div>
  </div>
</div>

@push("scripts")
<script>
(function () {
  if (!window.Chart) return;
  var labels = @json(($appAttend["labels"] ?? []));
  var apps   = @json(($appAttend["apps"]   ?? []));
  var attend = @json(($appAttend["attend"] ?? []));
  // normalize lengths
  var len = Math.max(labels.length, apps.length, attend.length);
  while (labels.length < len) labels.push("");
  while (apps.length   < len) apps.push(0);
  while (attend.length < len) attend.push(0);

  var el = document.getElementById("appsAttendChart");
  if (!el) return;
  new Chart(el, {
    type: "bar",
    data: {
      labels: labels,
      datasets: [
        { label: "Applications", data: apps },
        { label: "Attendance",  data: attend }
      ]
    },
    options: { responsive: true, maintainAspectRatio: false }
  });
})();
</script>
@endpush
BLADE;

  // Prefer to append (keeps any existing layout)
  $src .= "\n".$block."\n";
}

file_put_contents($part,$src);
echo "Patched: $part\n";
' || true

# --- hours_chart: ensure canvas + script ---
php -r '
$part = getenv("HOURS");
$src  = file_get_contents($part);

if (strpos($src, "id=\"hoursChart\"") === false) {
  $block = <<<'BLADE'

<div class="card shadow-sm h-100">
  <div class="card-body">
    <h6 class="mb-3">{{ __("Volunteer Hours") }}</h6>
    <div class="chart-box"><canvas id="hoursChart"></canvas></div>
  </div>
</div>

@push("scripts")
<script>
(function () {
  if (!window.Chart) return;
  // Accept multiple shapes and downshift safely
  var labels = @json(($hoursChart["labels"] ?? ($labels ?? [])));
  var values = @json(($hoursChart["values"] ?? ($hoursChart["data"] ?? [])));
  if (!Array.isArray(labels)) labels = [];
  if (!Array.isArray(values)) values = [];
  var len = Math.max(labels.length, values.length);
  while (labels.length < len) labels.push("");
  while (values.length < len) values.push(0);

  var el = document.getElementById("hoursChart");
  if (!el) return;
  new Chart(el, {
    type: "line",
    data: {
      labels: labels,
      datasets: [{ label: "Hours", data: values, fill: true, tension: 0.35 }]
    },
    options: { responsive: true, maintainAspectRatio: false }
  });
})();
</script>
@endpush
BLADE;

  $src .= "\n".$block."\n";
}

file_put_contents($part,$src);
echo "Patched: $part\n";
' || true

# --- small CSS so canvases have height ---
mkdir -p "$(dirname "$BRAND")"
touch "$BRAND"
grep -q ".chart-box" "$BRAND" || cat >> "$BRAND" <<'CSS'
.chart-box{ height:300px; position:relative; }
CSS

# --- rebuild + audit ---
php artisan view:clear >/dev/null && php artisan view:cache >/dev/null
php tools/audit_org_dashboard.php || true
