@extends('public.layout')
@section('title', $story->title ?? $item->title ?? 'Story')
@section('meta_description', $story->excerpt ?? $item->summary ?? 'Volunteer story')
@section('content')
  <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-3xl font-bold mb-4">{{ $story->title ?? $item->title ?? 'Story' }}</h1>
    @if(!empty($story->cover))
      <img loading="lazy" decoding="async" class="rounded-xl mb-6"
           src="{{ $story->cover }}" alt="{{ $story->title ?? 'Story image' }}">
    @endif
    <article class="prose prose-slate max-w-none">
      {!! $story->body ?? ($item->body ?? '<p>Story content coming soon.</p>') !!}
    </article>
  </section>
@endsection

@push('jsonld')
<script type="application/ld+json">
{
  "@context":"https://schema.org",
  "@type":"BlogPosting",
  "headline":"{{ ($story->title ?? $item->title ?? 'Story')|e }}",
  "description":"{{ ($story->excerpt ?? $item->summary ?? '')|e }}",
  "datePublished":"{{ $story->published_at ?? '' }}",
  "author":{"@type":"Person","name":"{{ ($story->author ?? 'SwaedUAE')|e }}"},
  "mainEntityOfPage":{"@type":"WebPage","@id":"{{ url()->current() }}"}
}
</script>
@endpush
