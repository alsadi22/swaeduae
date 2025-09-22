@extends('public.layout')
@section('title', $title ?? 'Page')
@section('content')
  <section class="mx-auto max-w-6xl px-4 py-10 prose">
    <h1 class="!mb-2">{{ $title ?? 'Page' }}</h1>
    @yield('page_content')
  </section>
@endsection
