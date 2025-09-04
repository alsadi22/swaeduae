@extends('admin.layout')
@section('title','Resend Certificate')

@section('content')
<div class="container-fluid py-3">
  <h1 class="mb-3">Resend Certificate Email</h1>
  @if(session('status')) <div class="alert alert-success">{{ session('status') }}</div>@endif
  @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div>@endif

  <div class="row g-3">
    <div class="col-md-6">
      <form method="post" action="{{ url('/admin/certificates/resend') }}" class="card card-body">
        @csrf
        <h5>By Code</h5>
        <input type="text" name="code" class="form-control mb-2" placeholder="SU250814-XXXXXX">
        <button class="btn btn-primary">Resend</button>
      </form>
    </div>
    <div class="col-md-6">
      <form method="post" action="{{ url('/admin/certificates/resend') }}" class="card card-body">
        @csrf
        <h5>By User + Opportunity</h5>
        <input type="number" name="user_id" class="form-control mb-2" placeholder="User ID">
        <input type="number" name="opportunity_id" class="form-control mb-2" placeholder="Opportunity ID">
        <button class="btn btn-primary">Resend</button>
      </form>
    </div>
  </div>
</div>
@endsection
