@extends('layouts.app')
@section('content')
<div class="container">
  <h1>Categories</h1>
  <div class="row">
    @forelse($categories as $category)
      <div class="col-md-3 mb-3">
        <a href="{{ route('categories.show',$category->id) }}" class="btn btn-outline-secondary w-100">{{ $category->name }}</a>
      </div>
    @empty
      <p>No categories found.</p>
    @endforelse
  </div>
</div>
@endsection
