@extends('admin.layout')
@section('title','Pending Organizations')

@section('content')
<h1>Pending Organizations</h1>
<table class="table">
  <thead>
    <tr>
      <th>Name</th><th>Email</th><th>Phone</th><th>Website</th><th>Emirate</th><th>Actions</th>
    </tr>
  </thead>
  <tbody>
  @forelse($pending as $org)
    <tr>
      <td>{{ $org->name }}</td>
      <td>{{ $org->email }}</td>
      <td>{{ $org->phone }}</td>
      <td><a href="{{ $org->website }}" target="_blank">{{ $org->website }}</a></td>
      <td>{{ $org->emirate }}</td>
      <td>
        <form method="POST" action="{{ route('admin.organizations.approve',$org) }}" style="display:inline">
          @csrf
          <button class="btn" type="submit">Approve</button>
        </form>
        <form method="POST" action="{{ route('admin.organizations.reject',$org) }}" style="display:inline" onsubmit="return confirm('Reject this organization?');">
          @csrf
          <button class="btn" type="submit" style="background:#dc3545">Reject</button>
        </form>
      </td>
    </tr>
  @empty
    <tr><td colspan="6">No pending organizations.</td></tr>
  @endforelse
  </tbody>
</table>

{{ $pending->links() }}
@endsection
