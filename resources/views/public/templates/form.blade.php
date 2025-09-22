@extends('public.layout')
@section('title', $title ?? 'Form')
@section('content')
  <section class="mx-auto max-w-3xl px-4 py-10">
    <h1 class="text-3xl font-bold mb-4">{{ $title ?? 'Form' }}</h1>
    <form method="POST" action="{{ $action ?? '#' }}" class="space-y-4">
      @csrf
      @yield('form_fields')
      <button type="submit" class="btn btn-brand">{{ $submitLabel ?? 'Submit' }}</button>
    </form>
  </section>
@endsection
