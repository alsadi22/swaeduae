@extends('admin.layout')
@section('title','Dashboard')
@section('content')
<div class="container">
  <h1 class="mb-4">Admin Dashboard</h1>
  <div class="list-group">
    <a href="{{ url('/admin/approvals') }}" class="list-group-item list-group-item-action">Approvals</a>
    <a href="{{ route('admin.hours.index') }}" class="list-group-item list-group-item-action">Hours</a>
    <a href="{{ route('admin.certificates.index') }}" class="list-group-item list-group-item-action">Certificates</a>
  </div>
</div>
@endsection
