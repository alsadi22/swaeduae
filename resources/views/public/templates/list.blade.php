@extends('public.layout')
@section('title', $title ?? 'List')
@section('content')
  <section class="mx-auto max-w-6xl px-4 py-10">
    <h1 class="text-3xl font-bold mb-4">{{ $title ?? 'List' }}</h1>
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
      @foreach($items as $item)
        <a href="{{ $item->url }}" class="card p-4 hover:shadow-md transition">
          <h2 class="text-xl font-semibold mb-2">{{ $item->title }}</h2>
          <p class="text-sm text-slate-700">{{ \Illuminate\Support\Str::limit($item->description ?? '', 120) }}</p>
        </a>
      @endforeach
    </div>
  </section>
@endsection
