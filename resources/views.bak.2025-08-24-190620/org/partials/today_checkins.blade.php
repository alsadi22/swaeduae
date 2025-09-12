@php /*__TODAY_CHECKINS_GUARDS__*/ $rows = collect($rows ?? []); $rows_count=$rows->count(); @endphp
@include('org.dashboard._safe_defaults')
@php $rows = $rows; @endphp
<div class="card shadow-sm h-100">
  <div class="card-body">
    <h6 class="mb-3">{{ __('Today check-ins') }}</h6>
    <ul class="list-clean">
      @forelse ($rows as $r)
        <li>
          <div class="d-flex gap-2 align-items-center"><span class="badge bg-primary-subtle">•</span><span>{{ $r['name'] ?? ($r->name ?? '—') }}</span></div>
          <span class="text-muted small">{{ $r['time'] ?? ($r->time ?? '') }}</span>
        </li>
      @empty
        <li class="text-muted">{{ __('No check-ins yet') }}</li>
      @endforelse
    </ul>
  </div>
</div>
