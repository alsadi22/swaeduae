@extends('layouts.app')
@section('content')
<div class="container">
  <h1>Our Partners</h1>
  <div class="row">
    @forelse($partners as $partner)
      <div class="col-md-3 text-center mb-4">
        <img src="{{ asset('storage/'.$partner->logo) }}" class="img-fluid mb-2" alt="{{ $partner->name }}">
        <h5>{{ $partner->name }}</h5>
      </div>
    @empty
      <p>No partners to display.</p>
    @endforelse
  </div>
</div>
@endsection
