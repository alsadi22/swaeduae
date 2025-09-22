@extends('public.layout')
@section('title','Opportunities')
@section('content')
  <section class="mx-auto max-w-6xl px-4 py-10">
    <h1 class="text-3xl font-bold mb-6">Opportunities</h1>
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
      @foreach(($items ?? [
        (object)['title'=>'Community Clean-Up','description'=>'Help clean parks and beaches.','url'=>'#'],
        (object)['title'=>'Food Drive','description'=>'Assist with sorting and packing.','url'=>'#'],
        (object)['title'=>'Virtual Mentoring','description'=>'Support students online.','url'=>'#'],
      ]) as $item)
        <div class="card p-5">
          <h2 class="text-xl font-semibold mb-2">{{ $item->title }}</h2>
          <p class="text-sm text-slate-700 mb-4">{{ \Illuminate\Support\Str::limit($item->description ?? '', 140) }}</p>
          <a href="{{ $item->url }}" class="btn btn-brand">Apply</a>
        </div>
      @endforeach
    </div>
  </section>
@endsection
