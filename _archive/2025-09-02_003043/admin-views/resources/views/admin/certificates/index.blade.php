@extends('admin.layout')
@section('admin')
<div class="container py-3">
  <h1 class="h4 mb-3">Certificates</h1>
  @if(!$hasTable)
    <div class="alert alert-warning">Table <code>certificates</code> not found.</div>
  @endif

  <form class="row g-2 mb-3" method="POST" action="{{ route('admin.certificates.issue') }}">
    @csrf
    <div class="col-auto">
      <input name="user_id" class="form-control form-control-sm" placeholder="User ID" required>
    </div>
    <div class="col-auto">
      <input name="hours" class="form-control form-control-sm" placeholder="Hours (optional)">
    </div>
    <div class="col-auto">
      <button class="btn btn-sm btn-primary">Issue</button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table align-middle">
      <thead><tr><th>ID</th><th>Serial</th><th>User</th><th>Hours</th><th>Issued</th><th></th></tr></thead>
      <tbody>
        @forelse($rows as $r)
        <tr>
          <td>{{ $r->id }}</td><td>{{ $r->serial }}</td><td>{{ $r->user_id }}</td>
          <td>{{ (float)($r->hours ?? 0) }}</td><td>{{ $r->issued_at }}</td>
          <td>
            <form class="d-inline" method="POST" action="{{ route('admin.certificates.reissue',$r->id) }}">@csrf
              <button class="btn btn-sm btn-outline-secondary">Reissue</button>
            </form>
          </td>
        </tr>
        @empty <tr><td colspan="6" class="text-muted">No certificates</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-2">{{ $rows->links() }}</div>
</div>
@endsection
