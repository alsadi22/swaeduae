@extends('admin.layout')
@section('admin')
<div class="container py-3">
  <h1 class="h4 mb-3">Applicants</h1>

  @if(!$hasTable)
    <div class="alert alert-warning">Table <code>applications</code> not found.</div>
  @endif

  <div class="d-flex gap-2 mb-3">
    <a href="?status=pending"  class="btn btn-sm {{ $status==='pending'?'btn-primary':'btn-outline-secondary' }}">Pending</a>
    <a href="?status=approved" class="btn btn-sm {{ $status==='approved'?'btn-primary':'btn-outline-secondary' }}">Approved</a>
    <a href="?status=declined" class="btn btn-sm {{ $status==='declined'?'btn-primary':'btn-outline-secondary' }}">Declined</a>
    <a href="{{ route('admin.applicants.export',['status'=>$status]) }}" class="btn btn-sm btn-outline-dark">Export CSV</a>
  </div>

  <form method="POST" action="{{ route('admin.applicants.bulk') }}">
    @csrf
    <input type="hidden" name="action" id="bulk_action" value="approve">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead><tr>
          <th><input type="checkbox" onclick="document.querySelectorAll('.cb').forEach(c=>c.checked=this.checked)"></th>
          <th>ID</th><th>User</th><th>Opportunity</th><th>Status</th><th>Applied</th><th></th>
        </tr></thead>
        <tbody>
        @forelse($rows as $r)
          <tr>
            <td><input type="checkbox" class="cb" name="ids[]" value="{{ $r->id }}"></td>
            <td>{{ $r->id }}</td>
            <td>{{ $r->user_id }}</td>
            <td>{{ $r->opportunity_id }}</td>
            <td><span class="badge bg-{{ $r->status==='approved'?'success':($r->status==='declined'?'danger':'secondary') }}">{{ $r->status }}</span></td>
            <td>{{ \Illuminate\Support\Carbon::parse($r->created_at)->format('Y-m-d H:i') }}</td>
            <td class="text-nowrap">
              <form class="d-inline" method="POST" action="{{ route('admin.applicants.approve',$r->id) }}">@csrf<button class="btn btn-sm btn-outline-success">Approve</button></form>
              <form class="d-inline" method="POST" action="{{ route('admin.applicants.decline',$r->id) }}">@csrf<button class="btn btn-sm btn-outline-danger">Decline</button></form>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-muted">No records</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-2 d-flex gap-2">
      <button class="btn btn-success btn-sm" onclick="document.getElementById('bulk_action').value='approve'">Bulk Approve</button>
      <button class="btn btn-danger btn-sm"  onclick="document.getElementById('bulk_action').value='decline'">Bulk Decline</button>
    </div>
  </form>

  <div class="mt-3">{{ $rows->links() }}</div>
</div>
@endsection
