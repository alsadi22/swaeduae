@php /*__RECENT_ACTIVITY_GUARDS__*/ $recentActivity = collect($recentActivity ?? []); @endphp
@include('org.dashboard._safe_defaults')
<div class="card shadow-sm h-100">
  <div class="card-body">
    <h6 class="mb-3">{{ __('Recent activity') }}</h6>
    <ul class="list-clean">
      @forelse ($recentActivity as $r)
        <li>
          <div class="d-flex gap-2 align-items-center">
            <span class="badge bg-light text-dark">{{ \Illuminate\Support\Str::substr($r['who'] ?? '—',0,3) }}</span>
            <strong>{{ $r['who'] ?? '—' }}</strong>
          </div>
          <span class="badge bg-secondary-subtle text-dark">{{ ($r['type'] ?? '') === 'attendance' ? __('Attendance') : __('Application') }}</span>
        </li>
      @empty
        <li class="text-muted">{{ __('No recent activity') }}</li>
      @endforelse
    </ul>
  </div>
</div>
