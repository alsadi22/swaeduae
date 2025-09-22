@extends('public.layout')
@section('title','Organizations')
@section('content')
  <section class="mx-auto max-w-6xl px-4 py-10">
    <h1 class="text-3xl font-bold mb-6">Organizations</h1>
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
      @foreach(($orgs ?? [
        (object)['name'=>'Red Crescent','desc'=>'Humanitarian aid and volunteer outreach.'],
        (object)['name'=>'Green UAE','desc'=>'Environmental protection and awareness.'],
        (object)['name'=>'Health First','desc'=>'Community health initiatives.'],
      ]) as $o)
        <div class="card p-5">
          <h2 class="text-xl font-semibold mb-1">{{ $o->name }}</h2>
          <p class="text-sm text-slate-700">{{ \Illuminate\Support\Str::limit($o->desc ?? '', 140) }}</p>
        </div>
      @foreach ($orgs ?? [] as $o)
      @endforeach
      @endforeach
    </div>
  </section>
@endsection
