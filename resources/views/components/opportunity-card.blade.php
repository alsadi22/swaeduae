@props([
  'title' => 'Community Cleanup',
  'org'   => 'SwaedUAE',
  'city'  => 'Abu Dhabi',
  'date'  => 'Next Saturday',
  'hours' => '3–4 hours',
  'img'   => '/images/placeholder-op.jpg',
  'link'  => '/org/login',
])
<article class="op-card">
  <a href="{{ $link }}" class="op-card__image" aria-label="View opportunity {{ $title }}">
    <img src="{{ $img }}" alt="" loading="lazy">
  </a>
  <div class="op-card__body">
    <h3 class="h6 mb-1"><a href="{{ $link }}">{{ $title }}</a></h3>
    <div class="text-muted small">{{ $org }} · {{ $city }}</div>
    <div class="chips mt-2">
      <span class="chip">{{ $date }}</span>
      <span class="chip">{{ $hours }}</span>
    </div>
    <div class="mt-3 d-flex gap-2">
      <a class="btn btn-primary btn-sm" href="{{ $link }}">Apply</a>
      <a class="btn btn-outline-primary btn-sm" href="{{ $link }}">Details</a>
    </div>
  </div>
</article>
