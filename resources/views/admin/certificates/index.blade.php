@extends('layouts.admin')
@section('title','Certificates')
@section('content')
  <h1 class="mb-3">Certificates</h1>
  @if(Route::has('admin.certificates.issue'))
    <form class="row gy-2 gx-2 align-items-end mb-3" method="POST" action="{{ route('admin.certificates.issue') }}">@csrf
      <div class="col-auto">
        <label class="form-label">User ID</label>
        <input class="form-control" name="user_id" required>
      </div>
      <div class="col-auto">
        <label class="form-label">Opportunity ID</label>
        <input class="form-control" name="opportunity_id" required>
      </div>
      <div class="col-auto">
        <button class="btn btn-success">Issue</button>
      </div>
    </form>
  @endif

  @section('row-actions')
    @php $id = $r['id'] ?? null; @endphp
    @if($id && Route::has('admin.certificates.reissue'))
      <form method="POST" action="{{ route('admin.certificates.reissue',$id) }}" class="d-inline">@csrf
        <button class="btn btn-sm btn-outline-primary">Reissue</button>
      </form>
    @endif
  @endsection

  @include('admin._table', ['rows' => $certificates ?? $items ?? []])
@endsection
