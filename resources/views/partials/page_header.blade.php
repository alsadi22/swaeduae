<div class="bg-light py-4 border-bottom" style="background: rgba(214,169,157,0.1);">
  <div class="container">
    <h1 class="h3 mb-1">{{ $title ?? '' }}</h1>
    @isset($subtitle)
      <p class="text-muted mb-0">{{ $subtitle }}</p>
    @endisset
  </div>
</div>
