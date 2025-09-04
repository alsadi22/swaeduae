@extends('admin.layout')

@section('content')
<div class="container py-4">
  <h1 class="h3 mb-3">{{ $title }}</h1>

  <div class="card mb-4">
    <div class="card-header">Totals by volunteer</div>
    <div class="table-responsive">
      <table class="table table-sm mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>Volunteer</th>
            <th>Email</th>
            <th class="text-end">Sessions</th>
            <th class="text-end">Total hours</th>
            <th class="text-nowrap">First</th>
            <th class="text-nowrap">Last</th>
          </tr>
        </thead>
        <tbody>
          @forelse($totals as $i => $row)
            <tr>
              <td>{{ $i+1 }}</td>
              <td>{{ $row->name ?? ('User '.$row->user_id) }}</td>
              <td>{{ $row->email }}</td>
              <td class="text-end">{{ $row->sessions }}</td>
              <td class="text-end">{{ number_format($row->total_hours,2) }}</td>
              <td class="text-nowrap">{{ \Illuminate\Support\Carbon::parse($row->first_at)->format('Y-m-d H:i') }}</td>
              <td class="text-nowrap">{{ \Illuminate\Support\Carbon::parse($row->last_at)->format('Y-m-d H:i') }}</td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-muted p-3">No hours recorded yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <div class="card-header">Recent sessions (last {{ count($sessions) }})</div>
    <div class="table-responsive">
      <table class="table table-sm mb-0">
        <thead>
          <tr>
            <th>ID</th>
            <th>When</th>
            <th>Volunteer</th>
            <th>Opportunity</th>
            <th class="text-end">Hours</th>
            <th>Note</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sessions as $s)
            <tr>
              <td>{{ $s->id }}</td>
              <td class="text-nowrap">{{ \Illuminate\Support\Carbon::parse($s->created_at)->format('Y-m-d H:i') }}</td>
              <td>{{ $s->user_name ?? ('User '.$s->user_id) }}</td>
              <td>{{ $s->opp_name ?? ($s->opportunity_id ? ('#'.$s->opportunity_id) : '—') }}</td>
              <td class="text-end">{{ number_format($s->hours,2) }}</td>
              <td class="text-truncate" style="max-width:420px">{{ $s->note }}</td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-muted p-3">No sessions yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-3">
    <a href="{{ route('admin.hours.all') }}" class="btn btn-outline-secondary btn-sm">All opportunities</a>
    @if($scopeId)
      <a href="{{ url('/admin/opportunities/'.$scopeId) }}" class="btn btn-outline-secondary btn-sm">Back to opportunity</a>
    @endif
  </div>
</div>
@endsection
