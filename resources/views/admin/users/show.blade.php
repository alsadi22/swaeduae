@extends('layouts.admin')
@section('title','User')
@section('content')
  <h1 class="mb-3">User #{{ $user->id ?? '' }}</h1>
  <div class="card p-3">
    <pre class="mb-3" style="white-space:pre-wrap">{{ json_encode($user ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
    @if(Route::has('admin.users.toggle') && isset($user->id))
      <form method="POST" action="{{ route('admin.users.toggle', $user->id) }}">@csrf
        <button class="btn btn-warning">Toggle Status</button>
      </form>
    @endif
  </div>
@endsection
