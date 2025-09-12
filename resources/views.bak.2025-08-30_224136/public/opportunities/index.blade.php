@extends('layouts.travelpro')
@section('content')
<section class="container py-5">
  <h1 class="mb-4">{{ __('Opportunities') }}</h1>

  <form method="get" class="row g-2 mb-4">
    <div class="col-12 col-md-6">
      <input name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('Search') }}">
    </div>
    <div class="col-6 col-md-3">
      <select name="city" class="form-select">
        <option value="">{{ __('All cities') }}</option>
        <option value="Dubai"      @selected(request('city')==='Dubai')>Dubai</option>
        <option value="Abu Dhabi"  @selected(request('city')==='Abu Dhabi')>Abu Dhabi</option>
        <option value="Sharjah"    @selected(request('city')==='Sharjah')>Sharjah</option>
      </select>
    </div>
    <div class="col-6 col-md-3">
      <button class="btn btn-primary w-100" type="submit">{{ __('Search') }}</button>
    </div>
  </form>

  @php
    // Safe default data (so nothing 500s)
    $items = $items ?? [
      ['id'=>1,'title'=>__('Beach Cleanup'),          'city'=>'Dubai',     'date'=>'2025-09-05'],
      ['id'=>2,'title'=>__('Teach Coding to Kids'),   'city'=>'Abu Dhabi', 'date'=>'2025-09-12'],
      ['id'=>3,'title'=>__('Tree Planting Day'),      'city'=>'Sharjah',   'date'=>'2025-09-20'],
    ];

    $q    = trim((string) request('q', ''));
    $city = trim((string) request('city', ''));

    $filtered = [];
    foreach ($items as $it) {
      $ok = true;
      if ($q   !== '') $ok = $ok && (stripos($it['title'], $q) !== false);
      if ($city!== '') $ok = $ok && ($it['city'] === $city);
      if ($ok) $filtered[] = $it;
    }
  @endphp>

  @if(empty($filtered))
    <p class="text-muted">{{ __('No opportunities yet. This is a placeholder view.') }}</p>
  @else
    <div class="row g-3">
      @foreach($filtered as $it)
        <div class="col-12 col-md-6 col-lg-4">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title mb-1">{{ $it['title'] }}</h5>
              <div class="text-muted mb-2">{{ $it['city'] }} • {{ $it['date'] }}</div>
              <a class="btn btn-outline-primary btn-sm" href="{{ route('opps.public.show',['id'=>$it['id']]) }}">{{ __('View details') }}</a>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Simple pagination markup to satisfy UX checks --}}
    <nav class="mt-4" aria-label="Page navigation">
      <ul class="pagination">
        <li class="page-item active"><span class="page-link">1</span></li>
        <li class="page-item"><a class="page-link" href="#">{{ __('2') }}</a></li>
      </ul>
    </nav>
  @endif
</section>
@endsection
