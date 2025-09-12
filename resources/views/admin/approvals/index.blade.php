@extends('admin.layout')
@section('title', 'Pending Organizations')
@section('content')
<h1>Pending Organizations</h1>
@if(session('status'))
  <div class="alert alert-success">Action: {{ session('status') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif
<div class="mb-3">
  <a href="{{ route('admin.approvals.export') }}" class="btn btn-primary">Export CSV</a>
</div>
<table class="table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Org Name</th>
      <th>Email</th>
      <th>Org Code</th>
      <th>Emirate</th>
      <th>Created</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    @forelse($pending as $row)
    <tr>
      <td>{{ $row->id }}</td>
      <td>{{ $row->org_name }}</td>
      <td>{{ $row->user_email }}</td>
      <td>{{ $row->org_code }}</td>
      <td>{{ $row->emirate }}</td>
      <td>{{ $row->created_at }}</td>
      <td>
        <form method="POST" action="{{ route('admin.approvals.orgs.approve', ['id'=>$row->id]) }}" style="display:inline">@csrf
          <button class="btn">Approve</button>
        </form>
        <form method="POST" action="{{ route('admin.approvals.orgs.reject', ['id'=>$row->id]) }}" style="display:inline">@csrf
          <button class="btn outline">Reject</button>
        </form>
      </td>
    </tr>
    @empty
    <tr><td colspan="7">No pending organizations.</td></tr>
    @endforelse
  </tbody>
</table>
@endsection
