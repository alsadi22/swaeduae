@extends('layouts.admin')
@section('title','Approvals')
@section('content')
@if(session('status'))
  <div class="alert alert-success">{{ session('status') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
<div class="card">
  <div class="card-header">Pending Organizations</div>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Created</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orgs as $org)
        <tr>
          <td>{{ $org->org_name }}</td>
          <td>{{ $org->user->email ?? '' }}</td>
          <td>{{ $org->created_at?->format('Y-m-d') }}</td>
          <td class="text-end">
            <form action="{{ route('admin.approvals.orgs.approve', $org->id) }}" method="post" class="d-inline">
              @csrf
              <button class="btn btn-success btn-sm">Approve</button>
            </form>
            <form action="{{ route('admin.approvals.orgs.decline', $org->id) }}" method="post" class="d-inline">
              @csrf
              <button class="btn btn-danger btn-sm">Decline</button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center text-muted">No pending organizations.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
