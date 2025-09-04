@extends('layouts.admin')
@section('title','Users')
@section('content')
  <h1 class="mb-3">Users</h1>
  @section('row-actions')
    @php $id = $r['id'] ?? null; @endphp
    @if($id)
      <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.users.show',$id) }}">View</a>
      @if(Route::has('admin.users.toggle'))
        <form method="POST" action="{{ route('admin.users.toggle',$id) }}" class="d-inline">@csrf
          <button class="btn btn-sm btn-warning">Toggle</button>
        </form>
      @endif
    @endif
  @endsection
  @include('admin._table', ['rows' => $users ?? $items ?? $rows ?? []])
@endsection
