<div class="card shadow-sm mt-3">
  <div class="card-header d-flex gap-2 align-items-center">
    <strong>{{ __('Hours Trend') }}</strong>
    <small class="text-muted">{{ __('Last 30 & 90 days') }}</small>
  </div>
  <div class="card-body">
    <canvas id="trend30" height="120"></canvas>
    <hr>
    <canvas id="trend90" height="120"></canvas>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', ()=>{
  new Chart(document.getElementById('trend30'), { type:'line', data:{ labels:@json($trend30_labels??[]), datasets:[{ label:'{{ __("Hours (30d)") }}', data:@json($trend30_data??[]) }]}, options:{responsive:true} });
  new Chart(document.getElementById('trend90'), { type:'line', data:{ labels:@json($trend90_labels??[]), datasets:[{ label:'{{ __("Hours (90d)") }}', data:@json($trend90_data??[]) }]}, options:{responsive:true} });
});
</script>
