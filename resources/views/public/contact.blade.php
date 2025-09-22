@extends('public.layout')
@section('title','Contact')

@section('content')
<section class="py-16">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight">Contact Us</h1>
    <p class="mt-4 text-lg text-gray-600">Have a question about volunteering or partnerships? Send us a message.</p>

    <div class="mt-8 max-w-2xl">
      <div class="rounded-2xl border shadow-sm p-6">
        <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
          @csrf
          <div>
            <label class="block text-sm font-medium">Name</label>
            <input name="name" type="text" required class="mt-1 w-full rounded-xl border px-3 py-2" placeholder="Your name">
          </div>
          <div>
            <label class="block text-sm font-medium">Email</label>
            <input name="email" type="email" required class="mt-1 w-full rounded-xl border px-3 py-2" placeholder="you@example.com">
          </div>
          <div>
            <label class="block text-sm font-medium">Message</label>
            <textarea name="message" rows="5" required class="mt-1 w-full rounded-xl border px-3 py-2" placeholder="How can we help?"></textarea>
          </div>
          <div class="pt-2">
            <button type="submit" class="inline-flex items-center rounded-2xl border px-5 py-3 font-semibold shadow-sm hover:shadow transition">
              Send Message
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
@endsection
