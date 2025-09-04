@extends("org.layout")


<div class="row g-3 align-items-stretch mb-3">
  <div class="col-6 col-md-3">
    <div class="card shadow-sm h-100"><div class="card-body">
      <div class="text-muted small">{{ __('Slot Cap') }}</div>
      <div class="h4 mb-0">{{ $cap ?: '—' }}</div>
    </div></div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card shadow-sm h-100"><div class="card-body">
      <div class="text-muted small">{{ __('Shortlisted') }}</div>
      <div class="h4 mb-0">{{ $shortlisted }}</div>
    </div></div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card shadow-sm h-100"><div class="card-body">
      <div class="text-muted small">{{ __('Approved') }}</div>
      <div class="h4 mb-0">{{ $approved }}</div>
    </div></div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card shadow-sm h-100"><div class="card-body">
      <div class="text-muted small">{{ __('Pending') }}</div>
      <div class="h4 mb-0">{{ $pending }}</div>
    </div></div>
  </div>
</div>
