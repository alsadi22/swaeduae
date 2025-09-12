@extends('admin.layout')

@section('admin')
<div class="container py-3">
  <h1 class="h4 mb-3">Organizations</h1>

  @if(!$hasTable)
    <div class="alert alert-warning">Table <code>organizations</code> not found. This page will populate once the table is present.</div>
  @endif

  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">Pending</div>
        <div class="card-body p-0">
          <table class="table mb-0 align-middle">
            <thead><tr><th>ID</th><th>Name</th><th>Email</th><th></th></tr></thead>
            <tbody>
              @forelse($pending as $o)
              <tr>
                <td>{{ $o->id }}</td>
                <td>{{ $o->name ?? '—' }}</td>
                <td>{{ $o->email ?? '—' }}</td>
                <td class="text-nowrap">
                  <form class="d-inline" method="POST" action="{{ route('admin.organizations.approve',$o->id) }}">
                    @csrf
                    <button class="btn btn-sm btn-success">Approve</button>
                  </form>
                </td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-muted">No pending organizations</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">Active</div>
        <div class="card-body p-0">
          <table class="table mb-0 align-middle">
            <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Status</th><th></th></tr></thead>
            <tbody>
              @forelse($active as $o)
              <tr>
                <td>{{ $o->id }}</td>
                <td>{{ $o->name ?? '—' }}</td>
                <td>{{ $o->email ?? '—' }}</td>
                <td>{{ $o->status ?? 'active' }}</td>
                <td class="text-nowrap">
                  <form class="d-inline" method="POST" action="{{ route('admin.organizations.suspend',$o->id) }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger">Suspend</button>
                  </form>
                </td>
              </tr>
              @empty
              <tr><td colspan="5" class="text-muted">No active organizations</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
