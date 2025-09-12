@extends('public.layout')
@section('title', ucfirst(request()->path()))
@section('content')
<div class="container py-5">
  <h1 class="mb-3">{{ ucfirst(request()->path()) }}</h1>
  <p>Content coming soon.</p>
</div>
@endsection
