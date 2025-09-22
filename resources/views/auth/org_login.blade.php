@extends('public.layout')
@section('title','Organization Login')
@section('content')
  <section class="mx-auto max-w-md px-4 py-12">
    <h1 class="text-3xl font-bold mb-6">Organization Login</h1>
    <form method="POST" action="{{ url('/org/login') }}" class="space-y-4">
      @csrf
      <div>
        <label class="block text-sm mb-1">Email</label>
        <input name="email" type="email" class="w-full rounded-xl border border-slate-300 px-3 py-2" required>
      </div>
      <div>
        <label class="block text-sm mb-1">Password</label>
        <input name="password" type="password" class="w-full rounded-xl border border-slate-300 px-3 py-2" required>
      </div>
      <button type="submit" class="btn btn-brand w-full">Login</button>
    </form>
  </section>
@endsection
