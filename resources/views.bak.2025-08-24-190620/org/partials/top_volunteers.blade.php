<div class="card shadow-sm mt-3">
  <div class="card-header"><strong>{{ __('Top Volunteers (Total Hours)') }}</strong></div>
  <div class="table-responsive">
    <table class="table table-sm mb-0">
      <thead><tr><th>#</th><th>{{ __('Name') }}</th><th>{{ __('Hours') }}</th></tr></thead>
      <tbody>
        @forelse(($topVolunteers ?? []) as $i => $r)
          <tr><td>{{ $i+1 }}</td><td>{{ $r->name }}</td><td>{{ round(($r->mins ?? 0)/60,2) }}</td></tr>
        @empty
          <tr><td colspan="3" class="text-center text-muted p-3">{{ __('No data') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
