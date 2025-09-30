@extends('public.layout')
@section('title', 'Story Show')
@section('content')
<div class="container">
  <h1>{{ $story->title }}</h1>
  <p>{{ $story->content }}</p>
</div>
@endsection
