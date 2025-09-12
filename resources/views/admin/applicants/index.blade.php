@extends('admin.layout')
@section('title','Applicants')
@section('content')
  <h1 class="mb-3">Applicants</h1>

  @if(Route::has('admin.applicants.bulk'))
  <form method="POST" action="{{ route('admin.applicants.bulk') }}" class="mb-3">
    @csrf
    <div class="input-group">
      <input class="form-control" name="ids" placeholder="IDs comma-separated e.g. 12,13,14">
      <select class="form-select" name="action">
        <option value="approve">Approve</option>
        <option value="decline">Decline</option>
      </select>
      <button class="btn btn-primary">Run</button>
    </div>
  </form>
  @endif

  @section('row-actions')
    @php $id = $r['id'] ?? null; @endphp
    @if($id)
      @if(Route::has('admin.applicants.approve'))
        <form method="POST" action="{{ route('admin.applicants.approve',$id) }}" class="d-inline">@csrf
          <button class="btn btn-sm btn-success">Approve</button>
        </form>
      @endif
      @if(Route::has('admin.applicants.decline'))
        <form method="POST" action="{{ route('admin.applicants.decline',$id) }}" class="d-inline">@csrf
          <button class="btn btn-sm btn-danger">Decline</button>
        </form>
      @endif
    @endif
  @endsection

  @include('admin._table', ['rows' => $applicants ?? $items ?? []])
@endsection
