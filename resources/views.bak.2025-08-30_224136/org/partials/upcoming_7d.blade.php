@extends("org.layout")
@php /*__UPCOMING7D_GUARDS__*/ $upcoming = $upcoming ?? []; $list = $list ?? $upcoming; $list = collect($list); $list_count=$list->count(); @endphp
@include('org.dashboard._safe_defaults')
@php $list = $upcoming; @endphp
<div class="card shadow-sm h-100">
  <div class="card-body">
    <h6 class="mb-3">{{ __('Upcoming (7 days)') }}</h6>
    <ul class="list-clean">
      @forelse ($list as $it)
        <li>
          <div class="d-flex gap-2 align-items-center"><span class="badge bg-primary-subtle">•</span><span>{{ $it['title'] ?? ($it->title ?? '—') }}</span></div>
          <span class="text-muted small">{{ $it['day'] ?? ($it->day ?? '') }}</span>
        </li>
      @empty
        <li class="text-muted">{{ __('No upcoming items') }}</li>
      @endforelse
    </ul>
  </div>
</div>
