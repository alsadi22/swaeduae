@php /*__KPIS_GUARDS__*/ $kpis = is_array($kpis ?? null) ? $kpis : []; $kpis = array_replace(['volunteers'=>0,'events'=>0,'hours'=>0,'applications'=>0], $kpis); $volunteersHosted   = (int) ($volunteersHosted   ?? ($kpis['volunteers']   ?? 0)); $totalHours         = (float)($totalHours         ?? ($kpis['hours']        ?? 0)); $upcomingOpps       = (int) ($upcomingOpps       ?? 0); $certificatesIssued = (int) ($certificatesIssued ?? 0); @endphp
@include('org.dashboard._safe_defaults')
<div class="row g-3">
  <div class="col-12 col-md-3">
    <div class="card kpi shadow-sm">
      <div class="card-body d-flex gap-3 align-items-center">
        <div class="icon bg-primary-subtle"><i class="ni ni-badge text-primary"></i></div>
        <div><div class="text-muted small">{{ __('CERTIFICATES ISSUED') }}</div><div class="h4 m-0">{{ $certificatesIssued }}</div></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card kpi shadow-sm">
      <div class="card-body d-flex gap-3 align-items-center">
        <div class="icon bg-info-subtle"><i class="ni ni-calendar-grid-58 text-info"></i></div>
        <div><div class="text-muted small">{{ __('UPCOMING OPPORTUNITIES') }}</div><div class="h4 m-0">{{ $upcomingOpps }}</div></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card kpi shadow-sm">
      <div class="card-body d-flex gap-3 align-items-center">
        <div class="icon bg-primary-subtle"><i class="ni ni-time-alarm text-primary"></i></div>
        <div><div class="text-muted small">{{ __('TOTAL HOURS CONTRIBUTED') }}</div><div class="h4 m-0">hrs {{ number_format($totalHours, 2) }}</div></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card kpi shadow-sm">
      <div class="card-body d-flex gap-3 align-items-center">
        <div class="icon bg-info-subtle"><i class="ni ni-single-02 text-info"></i></div>
        <div><div class="text-muted small">{{ __('TOTAL VOLUNTEERS') }}</div><div class="h4 m-0">{{ $volunteersHosted }}</div></div>
      </div>
    </div>
  </div>
</div>
