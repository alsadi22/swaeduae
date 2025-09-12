@extends('admin.layout')
@section('admin')
<div class="container py-3">
  <h1 class="h4 mb-3">Hours</h1>
  @if(!$hasTable)
    <div class="alert alert-warning">Table <code>hours</code> not found.</div>
  @endif

  <div class="mb-2">
    <a href="{{ route('admin.hours.export') }}" class="btn btn-sm btn-outline-dark">Export CSV</a>
  </div>

  <form method="POST" action="{{ route('admin.hours.bulkApprove') }}">
    @csrf
    <div class="table-responsive">
      <table class="table align-middle">
        <thead><tr>
          <th><input type="checkbox" onclick="document.querySelectorAll('.cb').forEach(c=>c.checked=this.checked)"></th>
          <th>ID</th><th>User</th><th>Opportunity</th><th>Hours</th><th>Status</th><th>Submitted</th>
        </tr></thead>
        <tbody>
          @forelse($rows as $r)
          <tr>
            <td><input type="checkbox" class="cb" name="ids[]" value="{{ $r->id }}"></td>
            <td>{{ $r->id }}</td>
            <td>{{ $r->user_id }}</td>
            <td>{{ $r->opportunity_id }}</td>
            <td>{{ (float)$r->hours }}</td>
            <td><span class="badge bg-{{ $r->status==='approved'?'success':($r->status==='pending'?'secondary':'danger') }}">{{ $r->status }}</span></td>
            <td>{{ \Illuminate\Support\Carbon::parse($r->created_at)->format('Y-m-d H:i') }}</td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-muted">No records</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <button class="btn btn-primary btn-sm">Bulk Approve</button>
  </form>

  <div class="mt-3">{{ $rows->links() }}</div>
</div>
@endsection
