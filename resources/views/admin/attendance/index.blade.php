@extends('layouts.admin')
@section('title','Attendance')
@section('content')
  <h1 class="mb-3">Attendance</h1>
  @include('admin._table', ['rows' => $attendance ?? $items ?? []])
@endsection
