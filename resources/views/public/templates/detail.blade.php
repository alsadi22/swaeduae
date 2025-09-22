@extends('public.layout')
@section('title', $item->title ?? 'Detail')
@section('content')
  <section class="mx-auto max-w-3xl px-4 py-10">
    <h1 class="text-3xl font-bold mb-4">{{ $item->title ?? 'Detail' }}</h1>
    @if(isset($item->image))
      <img src="{{ $item->image }}" alt="{{ $item->title }}" class="w-full rounded-2xl mb-6">
    @endif
    <p class="mb-6 text-slate-700">{{ $item->description ?? '' }}</p>
    @yield('detail_content')
  </section>
@endsection
