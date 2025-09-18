@extends('public.layout')
@section('title','Profile Settings')
@section('content')
<div class="py-5 bg-light">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card shadow-sm border-0">
          <div class="card-body p-4">
            <h1 class="h4 mb-3">Profile Settings</h1>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Name</label>
                <input class="form-control" value="{{ auth()->user()->name ?? '' }}" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label">Email</label>
                <input class="form-control" value="{{ auth()->user()->email ?? '' }}" readonly>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
