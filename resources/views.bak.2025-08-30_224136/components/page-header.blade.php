@props(['title' => '', 'subtitle' => null, 'actions' => null])
<section class="card border-0 shadow-sm rounded-4">
  <div class="card-body d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-2">
    <div>
      <h1 class="h4 fw-bold mb-1 text-navy">{{ $title }}</h1>
      @if($subtitle)<p class="text-muted mb-0">{{ $subtitle }}</p>@endif
    </div>
    <div>{{ $actions }}</div>
  </div>
</section>
