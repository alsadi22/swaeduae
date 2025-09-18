@extends('layouts.admin')
@section('title','Organizations')
@section('content')
  <h1 class="mb-3">Organizations</h1>
  @section('row-actions')
    @php $id = $r['id'] ?? null; @endphp
    @if($id)
      @if(Route::has('admin.organizations.approve'))
        <form method="POST" action="{{ route('admin.organizations.approve',$id) }}" class="d-inline">@csrf
          <button class="btn btn-sm btn-success">Approve</button>
        </form>
      @endif
      @if(Route::has('admin.organizations.suspend'))
        <form method="POST" action="{{ route('admin.organizations.suspend',$id) }}" class="d-inline">@csrf
          <button class="btn btn-sm btn-danger">Suspend</button>
        </form>
      @endif
    @endif
  @endsection
  @include('admin._table', ['rows' => $organizations ?? $items ?? []])
@endsection
