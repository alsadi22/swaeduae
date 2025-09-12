@extends('admin.layout')
@section('title','Reports')
@section('content')
  <h1 class="mb-3">Reports</h1>
  @if(Route::has('admin.reports.export'))
    <a class="btn btn-outline-primary mb-3" href="{{ route('admin.reports.export') }}">Export CSV</a>
  @endif
  @include('admin._table', ['rows' => $reports ?? $items ?? []])
@endsection
