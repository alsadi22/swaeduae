@extends('layouts.admin')
@section('title','Opportunities')
@section('content')
  <h1 class="mb-3">Opportunities</h1>
  @section('row-actions')
    @php $id = $r['id'] ?? $r['opportunity_id'] ?? null; @endphp
    @if($id)
      <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.opportunities.show',$id) }}">View</a>
      @if(Route::has('admin.hours.show')) <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.hours.show',$id) }}">Hours</a> @endif
      @if(Route::has('admin.attendance.qr')) <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.attendance.qr',$id) }}">QR</a> @endif
      @if(Route::has('admin.attendance.finalize'))
        <form method="POST" action="{{ route('admin.attendance.finalize',$id) }}" class="d-inline">@csrf
          <button class="btn btn-sm btn-warning">Finalize</button>
        </form>
      @endif
      @if(Route::has('admin.opportunities.destroy'))
        <form method="POST" action="{{ route('admin.opportunities.destroy',$id) }}" class="d-inline" onsubmit="return confirm('Delete opportunity?');">
          @csrf @method('DELETE')
          <button class="btn btn-sm btn-danger">Delete</button>
        </form>
      @endif
    @endif
  @endsection
  @include('admin._table', ['rows' => $opportunities ?? $items ?? []])
@endsection
