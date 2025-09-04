@extends('layouts.app')
@section('content')
<div class="container">
  <h1>Success Stories</h1>
  <div class="row">
    @forelse($stories as $story)
      <div class="col-md-4 mb-4">
        <div class="card h-100">
          <img src="{{ asset('storage/'.$story->image) }}" class="card-img-top" alt="{{ $story->title }}">
          <div class="card-body">
            <h5 class="card-title">{{ $story->title }}</h5>
            <p class="card-text">{{ Str::limit($story->content,120) }}</p>
            <a href="{{ route('stories.show',$story->id) }}" class="btn btn-outline-success btn-sm">Read More</a>
          </div>
        </div>
      </div>
    @empty
      <p>No stories available.</p>
    @endforelse
  </div>
</div>
@endsection
