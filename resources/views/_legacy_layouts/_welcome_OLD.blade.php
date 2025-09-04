<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}" @if(app()->getLocale()==='ar') dir="rtl" @endif>
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
@include('partials.meta')

<link href="{{ asset('css/site.css') }}" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>
  @include('partials.header')

  <main>
    <section class="hero">
      @if($appSettings['home_hero'])
        <img class="hero-img" src="{{ asset('storage/'.$appSettings['home_hero']) }}" alt="Hero">
      @endif

      <div class="container hero-overlay d-flex justify-content-center">
        <div class="glass-panel text-center px-4 py-4 py-md-5" style="max-width:680px;">
          <h1 class="fw-bold mb-2">{{ $appSettings['site_name'] }}</h1>
          <p class="lead mb-4">{{ $appSettings['site_tagline'] }}</p>
          <a href="{{ url('/opportunities') }}" class="btn btn-primary btn-lg px-4">Browse Opportunities</a>
        </div>
      </div>
    </section>

    @if(!empty($appSettings['home_gallery']))
    <section class="py-5">
      <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3 class="mb-0">Event Photos</h3>
          <a href="{{ url('/gallery') }}" class="btn btn-outline-primary btn-sm">View all</a>
        </div>
        <div class="row g-3 gallery">
          @foreach($appSettings['home_gallery'] as $path)
            <div class="col-6 col-md-4 col-lg-3">
              <img src="{{ asset('storage/'.$path) }}" alt="Event photo" data-bs-toggle="modal" data-bs-target="#lightbox" data-src="{{ asset('storage/'.$path) }}">
            </div>
          @endforeach
        </div>
      </div>
    </section>
    @endif
  </main>

  <footer class="py-4 border-top bg-light">
    <div class="container d-flex justify-content-between align-items-center">
      <div class="small text-muted">© {{ date('Y') }} {{ $appSettings['site_name'] }}</div>
      @include('partials.social')
    </div>
  </footer>

  <!-- Lightbox -->
  <div class="modal fade" id="lightbox" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content bg-black">
        <img id="lightboxImg" class="w-100" alt="Preview">
      </div>
    </div>
  </div>

  
  <script>
    const lb = document.getElementById('lightbox');
    lb?.addEventListener('show.bs.modal', e=>{
      const img = e.relatedTarget?.getAttribute('data-src');
      document.getElementById('lightboxImg').src = img || '';
    });
  </script>
</body>
</html>

{{-- === Latest items under hero === --}}
<div class="container my-5">

  {{-- Latest Opportunities --}}
  <div class="d-flex justify-content-between align-items-end mb-3">
    <h3 class="mb-0">Latest Opportunities</h3>
    <a href="{{ url('/opportunities') }}" class="btn btn-sm btn-outline-primary">View all</a>
  </div>
  <div class="row g-3">
    @forelse(($opps ?? collect()) as $o)
      <div class="col-12 col-sm-6 col-lg-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <div class="small text-muted">
              {{ $o->city ?? '—' }}
              @if($o->date) · {{ \Carbon\Carbon::parse($o->date)->format('M d, Y') }} @endif
            </div>
            <div class="fw-semibold mb-1">{{ $o->title }}</div>
            @if($o->start_time || $o->end_time)
              <div class="small text-muted">
                {{ $o->start_time ?? '' }}@if($o->end_time) – {{ $o->end_time }} @endif
              </div>
            @endif
            @if(!empty($o->category))
              <span class="badge bg-light text-dark mt-2">{{ $o->category }}</span>
            @endif
          </div>
        </div>
      </div>
    @empty
      <div class="col-12"><div class="text-muted">No opportunities yet.</div></div>
    @endforelse
  </div>

  {{-- Latest Events --}}
  <div class="d-flex justify-content-between align-items-end mt-5 mb-3">
    <h3 class="mb-0">Latest Events</h3>
    <a href="{{ url('/events') }}" class="btn btn-sm btn-outline-primary">View all</a>
  </div>
  <div class="row g-3">
    @forelse(($events ?? collect()) as $e)
      <div class="col-12 col-sm-6 col-lg-4">
        <div class="card h-100 shadow-sm">
          @if(!empty($e->poster_path))
            <img src="{{ asset('storage/'.$e->poster_path) }}" class="card-img-top" alt="{{ $e->title }}" style="object-fit:cover;height:160px">
          @endif
          <div class="card-body">
            <div class="small text-muted">
              {{ $e->city ?? '—' }}
              @if($e->date) · {{ \Carbon\Carbon::parse($e->date)->format('M d, Y') }} @endif
            </div>
            <div class="fw-semibold mb-1">{{ $e->title }}</div>
            @if($e->start_time || $e->end_time)
              <div class="small text-muted">
                {{ $e->start_time ?? '' }}@if($e->end_time) – {{ $e->end_time }} @endif
              </div>
            @endif
            @if(!empty($e->category))
              <span class="badge bg-light text-dark mt-2">{{ $e->category }}</span>
            @endif
          </div>
        </div>
      </div>
    @empty
      <div class="col-12"><div class="text-muted">No events yet.</div></div>
    @endforelse
  </div>

</div>

{{-- ===== NEW: Modern grid for Opportunities & Events (inspired by volunteers.ae) ===== --}}
<style>
  .vol-section{background:#042e49;border-radius:18px;padding:28px;color:#fff}
  .vol-head{font-weight:700;font-size:1.25rem;text-align:center}
  .vol-tabs{gap:.5rem;justify-content:center;margin:18px 0}
  .vol-tabs .btn{border-radius:999px;padding:.35rem 1rem}
  .vol-grid{display:grid;grid-template-columns:repeat(1,minmax(0,1fr));gap:16px}
  @media(min-width:768px){.vol-grid{grid-template-columns:repeat(2,1fr)}}
  @media(min-width:1200px){.vol-grid{grid-template-columns:repeat(4,1fr)}}
  .v-card{border-radius:18px;background:#0b3f60;overflow:hidden;border:1px solid rgba(255,255,255,.15);box-shadow:0 8px 24px rgba(0,0,0,.2)}
  .v-media{position:relative;height:170px;background:#133d57}
  .v-media img{width:100%;height:100%;object-fit:cover;display:block;filter:saturate(.95)}
  .v-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55),rgba(0,0,0,.1))}
  .v-badges{position:absolute;top:10px;left:10px;display:flex;gap6}
  .v-badge{background:rgba(255,255,255,.15);color:#fff;font-size:.75rem;border-radius:999px;padding:.2rem .55rem;backdrop-filter:blur(3px)}
  .v-body{background:#072c45;padding:14px}
  .v-title{font-weight:700;color:#fff;margin:0 0 .4rem 0;min-height:2.6em;line-height:1.3}
  .v-meta{font-size:.9rem;color:#d7e7f5;display:grid;gap:6px}
  .v-meta .row{display:flex;align-items:center;gap:8px}
  .v-meta .ico{width:18px;opacity:.9}
  .v-foot{display:flex;justify-content:space-between;align-items:center;padding:12px 14px;background:#06273c;border-top:1px solid rgba(255,255,255,.08)}
  .v-btn{background:#1081ff;border:none;color:#fff;border-radius:12px;padding:.4rem .9rem;font-weight:600}
  .v-btn:hover{filter:brightness(1.05)}
  .v-num{font-size:.85rem;color:#cde0f3}
  .text-muted-lite{color:#aac6da}
</style>

<div class="container my-4">
  <div class="vol-section">
    <div class="vol-head mb-2">Explore Opportunities</div>

    {{-- Tiny filter: All / On-site / Virtual --}}
    <div class="d-flex vol-tabs">
      <button class="btn btn-light btn-sm" data-filter="all">All</button>
      <button class="btn btn-outline-light btn-sm" data-filter="onsite">On-site</button>
      <button class="btn btn-outline-light btn-sm" data-filter="virtual">Virtual</button>
    </div>

    {{-- Opportunities grid --}}
    <div class="vol-grid" id="oppsGrid">
      @forelse(($opps ?? collect()) as $o)
        @php
          $isVirtual = false;
          $loc = strtolower(trim(($o->location ?? '') . ' ' . ($o->city ?? '')));
          if (str_contains($loc,'virtual') || str_contains($loc,'online') || str_contains($loc,'remote')) $isVirtual = true;
          $dateTxt = $o->date ? \Carbon\Carbon::parse($o->date)->format('M d, Y') : null;
        @endphp
        <div class="v-card" data-variant="{{ $isVirtual ? 'virtual':'onsite' }}">
          <div class="v-media">
            {{-- No poster for opportunities; use a subtle gradient --}}
            <div class="v-overlay"></div>
            <div class="v-badges">
              @if(!empty($o->category)) <span class="v-badge">{{ $o->category }}</span> @endif
              @if(!empty($o->region))   <span class="v-badge">{{ $o->region }}</span>   @endif
            </div>
          </div>
          <div class="v-body">
            <h6 class="v-title">{{ $o->title }}</h6>
            <div class="v-meta">
              @if($dateTxt)
                <div class="row"><svg class="ico" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2h2v2h6V2h2v2h3v18H4V4h3V2zm13 8H4v10h16V10z"/></svg><span>{{ $dateTxt }}</span></div>
              @endif
              @if($o->city || $o->location)
                <div class="row"><svg class="ico" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 0 1 7 7c0 5-7 13-7 13S5 14 5 9a7 7 0 0 1 7-7zm0 9.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/></svg>
                  <span>{{ $o->city ?? '' }} {{ $o->location ? '· '.$o->location : '' }}</span>
                </div>
              @endif
              @if($o->start_time || $o->end_time)
                <div class="row"><svg class="ico" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1a11 11 0 1 0 11 11A11.013 11.013 0 0 0 12 1Zm1 11h5v2h-7V6h2Z"/></svg>
                  <span>{{ $o->start_time ?? '' }} @if($o->end_time) – {{ $o->end_time }} @endif</span>
                </div>
              @endif
            </div>
          </div>
          <div class="v-foot">
            <div class="v-num">
              @if(!empty($o->slots)) <span class="text-muted-lite">{{ $o->slots }}</span> slots @endif
            </div>
            <a href="{{ url('/opportunities') }}" class="v-btn">Register</a>
          </div>
        </div>
      @empty
        <div class="text-center w-100 text-muted">No opportunities yet.</div>
      @endforelse
    </div>

    {{-- Divider --}}
    <hr class="my-4" style="border-color:rgba(255,255,255,.15)">

    <div class="vol-head mb-3">Latest Events</div>

    {{-- Events grid --}}
    <div class="vol-grid">
      @forelse(($events ?? collect()) as $e)
        @php
          $eDate = $e->date ? \Carbon\Carbon::parse($e->date)->format('M d, Y') : null;
        @endphp
        <div class="v-card">
          <div class="v-media">
            @if(!empty($e->poster_path))
              <img src="{{ asset('storage/'.$e->poster_path) }}" alt="{{ $e->title }}">
            @endif
            <div class="v-overlay"></div>
            <div class="v-badges">
              @if(!empty($e->category)) <span class="v-badge">{{ $e->category }}</span> @endif
              @if(!empty($e->region))   <span class="v-badge">{{ $e->region }}</span>   @endif
            </div>
          </div>
          <div class="v-body">
            <h6 class="v-title">{{ $e->title }}</h6>
            <div class="v-meta">
              @if($eDate)
                <div class="row"><svg class="ico" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2h2v2h6V2h2v2h3v18H4V4h3V2zm13 8H4v10h16V10z"/></svg><span>{{ $eDate }}</span></div>
              @endif
              @if($e->city || $e->location)
                <div class="row"><svg class="ico" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 0 1 7 7c0 5-7 13-7 13S5 14 5 9a7 7 0 0 1 7-7zm0 9.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/></svg>
                  <span>{{ $e->city ?? '' }} {{ $e->location ? '· '.$e->location : '' }}</span>
                </div>
              @endif
              @if($e->start_time || $e->end_time)
                <div class="row"><svg class="ico" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1a11 11 0 1 0 11 11A11.013 11.013 0 0 0 12 1Zm1 11h5v2h-7V6h2Z"/></svg>
                  <span>{{ $e->start_time ?? '' }} @if($e->end_time) – {{ $e->end_time }} @endif</span>
                </div>
              @endif
            </div>
          </div>
          <div class="v-foot">
            <div class="v-num">
              @if(!empty($e->capacity)) <span class="text-muted-lite">{{ $e->capacity }}</span> seats @endif
            </div>
            <a href="{{ url('/events') }}" class="v-btn">View</a>
          </div>
        </div>
      @empty
        <div class="text-center w-100 text-muted">No events yet.</div>
      @endforelse
    </div>
  </div>
</div>

<script>
  // simple client-side filter by "virtual" vs "onsite"
  (function(){
    const btns = document.querySelectorAll('[data-filter]');
    const grid = document.getElementById('oppsGrid');
    if(!grid) return;
    btns.forEach(b=>{
      b.addEventListener('click',()=>{
        const mode = b.getAttribute('data-filter');
        btns.forEach(x=>x.classList.remove('btn-light')); // reset styles
        btns.forEach(x=>x.classList.add('btn-outline-light'));
        b.classList.remove('btn-outline-light');
        b.classList.add('btn-light');

        grid.querySelectorAll('.v-card').forEach(card=>{
          const v = card.getAttribute('data-variant');
          card.style.display = (mode==='all' || v===mode) ? '' : 'none';
        });
      });
    });
  })();
</script>
{{-- ===== END modern grid ===== --}}

{{-- ===== NEW: Modern grid for Opportunities & Events (inspired by volunteers.ae) ===== --}}
<style>
  .vol-section{background:#042e49;border-radius:18px;padding:28px;color:#fff}
  .vol-head{font-weight:700;font-size:1.25rem;text-align:center}
  .vol-tabs{gap:.5rem;justify-content:center;margin:18px 0}
  .vol-tabs .btn{border-radius:999px;padding:.35rem 1rem}
  .vol-grid{display:grid;grid-template-columns:repeat(1,minmax(0,1fr));gap:16px}
  @media(min-width:768px){.vol-grid{grid-template-columns:repeat(2,1fr)}}
  @media(min-width:1200px){.vol-grid{grid-template-columns:repeat(4,1fr)}}
  .v-card{border-radius:18px;background:#0b3f60;overflow:hidden;border:1px solid rgba(255,255,255,.15);box-shadow:0 8px 24px rgba(0,0,0,.2)}
  .v-media{position:relative;height:170px;background:#133d57}
  .v-media img{width:100%;height:100%;object-fit:cover;display:block;filter:saturate(.95)}
  .v-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55),rgba(0,0,0,.1))}
  .v-badges{position:absolute;top:10px;left:10px;display:flex;gap6}
  .v-badge{background:rgba(255,255,255,.15);color:#fff;font-size:.75rem;border-radius:999px;padding:.2rem .55rem;backdrop-filter:blur(3px)}
  .v-body{background:#072c45;padding:14px}
  .v-title{font-weight:700;color:#fff;margin:0 0 .4rem 0;min-height:2.6em;line-height:1.3}
  .v-meta{font-size:.9rem;color:#d7e7f5;display:grid;gap:6px}
  .v-meta .row{display:flex;align-items:center;gap:8px}
  .v-meta .ico{width:18px;opacity:.9}
  .v-foot{display:flex;justify-content:space-between;align-items:center;padding:12px 14px;background:#06273c;border-top:1px solid rgba(255,255,255,.08)}
  .v-btn{background:#1081ff;border:none;color:#fff;border-radius:12px;padding:.4rem .9rem;font-weight:600}
  .v-btn:hover{filter:brightness(1.05)}
  .v-num{font-size:.85rem;color:#cde0f3}
  .text-muted-lite{color:#aac6da}
</style>

<div class="container my-4">
  <div class="vol-section">
    <div class="vol-head mb-2">Explore Opportunities</div>

    {{-- Tiny filter: All / On-site / Virtual --}}
    <div class="d-flex vol-tabs">
      <button class="btn btn-light btn-sm" data-filter="all">All</button>
      <button class="btn btn-outline-light btn-sm" data-filter="onsite">On-site</button>
      <button class="btn btn-outline-light btn-sm" data-filter="virtual">Virtual</button>
    </div>

    {{-- Opportunities grid --}}
    <div class="vol-grid" id="oppsGrid">
      @forelse(($opps ?? collect()) as $o)
        @php
          $isVirtual = false;
          $loc = strtolower(trim(($o->location ?? '') . ' ' . ($o->city ?? '')));
          if (str_contains($loc,'virtual') || str_contains($loc,'online') || str_contains($loc,'remote')) $isVirtual = true;
          $dateTxt = $o->date ? \Carbon\Carbon::parse($o->date)->format('M d, Y') : null;
        @endphp
        <div class="v-card" data-variant="{{ $isVirtual ? 'virtual':'onsite' }}">
          <div class="v-media">
            {{-- No poster for opportunities; use a subtle gradient --}}
            <div class="v-overlay"></div>
            <div class="v-badges">
              @if(!empty($o->category)) <span class="v-badge">{{ $o->category }}</span> @endif
              @if(!empty($o->region))   <span class="v-badge">{{ $o->region }}</span>   @endif
            </div>
          </div>
          <div class="v-body">
            <h6 class="v-title">{{ $o->title }}</h6>
            <div class="v-meta">
              @if($dateTxt)
                <div class="row"><svg class="ico" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2h2v2h6V2h2v2h3v18H4V4h3V2zm13 8H4v10h16V10z"/></svg><span>{{ $dateTxt }}</span></div>
              @endif
              @if($o->city || $o->location)
                <div class="row"><svg class="ico" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 0 1 7 7c0 5-7 13-7 13S5 14 5 9a7 7 0 0 1 7-7zm0 9.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/></svg>
                  <span>{{ $o->city ?? '' }} {{ $o->location ? '· '.$o->location : '' }}</span>
                </div>
              @endif
              @if($o->start_time || $o->end_time)
                <div class="row"><svg class="ico" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1a11 11 0 1 0 11 11A11.013 11.013 0 0 0 12 1Zm1 11h5v2h-7V6h2Z"/></svg>
                  <span>{{ $o->start_time ?? '' }} @if($o->end_time) – {{ $o->end_time }} @endif</span>
                </div>
              @endif
            </div>
          </div>
          <div class="v-foot">
            <div class="v-num">
              @if(!empty($o->slots)) <span class="text-muted-lite">{{ $o->slots }}</span> slots @endif
            </div>
            <a href="{{ url('/opportunities') }}" class="v-btn">Register</a>
          </div>
        </div>
      @empty
        <div class="text-center w-100 text-muted">No opportunities yet.</div>
      @endforelse
    </div>

    {{-- Divider --}}
    <hr class="my-4" style="border-color:rgba(255,255,255,.15)">

    <div class="vol-head mb-3">Latest Events</div>

    {{-- Events grid --}}
    <div class="vol-grid">
      @forelse(($events ?? collect()) as $e)
        @php
          $eDate = $e->date ? \Carbon\Carbon::parse($e->date)->format('M d, Y') : null;
        @endphp
        <div class="v-card">
          <div class="v-media">
            @if(!empty($e->poster_path))
              <img src="{{ asset('storage/'.$e->poster_path) }}" alt="{{ $e->title }}">
            @endif
            <div class="v-overlay"></div>
            <div class="v-badges">
              @if(!empty($e->category)) <span class="v-badge">{{ $e->category }}</span> @endif
              @if(!empty($e->region))   <span class="v-badge">{{ $e->region }}</span>   @endif
            </div>
          </div>
          <div class="v-body">
            <h6 class="v-title">{{ $e->title }}</h6>
            <div class="v-meta">
              @if($eDate)
                <div class="row"><svg class="ico" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2h2v2h6V2h2v2h3v18H4V4h3V2zm13 8H4v10h16V10z"/></svg><span>{{ $eDate }}</span></div>
              @endif
              @if($e->city || $e->location)
                <div class="row"><svg class="ico" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 0 1 7 7c0 5-7 13-7 13S5 14 5 9a7 7 0 0 1 7-7zm0 9.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/></svg>
                  <span>{{ $e->city ?? '' }} {{ $e->location ? '· '.$e->location : '' }}</span>
                </div>
              @endif
              @if($e->start_time || $e->end_time)
                <div class="row"><svg class="ico" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1a11 11 0 1 0 11 11A11.013 11.013 0 0 0 12 1Zm1 11h5v2h-7V6h2Z"/></svg>
                  <span>{{ $e->start_time ?? '' }} @if($e->end_time) – {{ $e->end_time }} @endif</span>
                </div>
              @endif
            </div>
          </div>
          <div class="v-foot">
            <div class="v-num">
              @if(!empty($e->capacity)) <span class="text-muted-lite">{{ $e->capacity }}</span> seats @endif
            </div>
            <a href="{{ url('/events') }}" class="v-btn">View</a>
          </div>
        </div>
      @empty
        <div class="text-center w-100 text-muted">No events yet.</div>
      @endforelse
    </div>
  </div>
</div>

<script>
  // simple client-side filter by "virtual" vs "onsite"
  (function(){
    const btns = document.querySelectorAll('[data-filter]');
    const grid = document.getElementById('oppsGrid');
    if(!grid) return;
    btns.forEach(b=>{
      b.addEventListener('click',()=>{
        const mode = b.getAttribute('data-filter');
        btns.forEach(x=>x.classList.remove('btn-light')); // reset styles
        btns.forEach(x=>x.classList.add('btn-outline-light'));
        b.classList.remove('btn-outline-light');
        b.classList.add('btn-light');

        grid.querySelectorAll('.v-card').forEach(card=>{
          const v = card.getAttribute('data-variant');
          card.style.display = (mode==='all' || v===mode) ? '' : 'none';
        });
      });
    });
  })();
</script>
{{-- ===== END modern grid ===== --}}

{{-- ===== NEW: Modern grid for Opportunities & Events (inspired by volunteers.ae) ===== --}}
<style>
  .vol-section{background:#042e49;border-radius:18px;padding:28px;color:#fff}
  .vol-head{font-weight:700;font-size:1.25rem;text-align:center}
  .vol-tabs{gap:.5rem;justify-content:center;margin:18px 0}
  .vol-tabs .btn{border-radius:999px;padding:.35rem 1rem}
  .vol-grid{display:grid;grid-template-columns:repeat(1,minmax(0,1fr));gap:16px}
  @media(min-width:768px){.vol-grid{grid-template-columns:repeat(2,1fr)}}
  @media(min-width:1200px){.vol-grid{grid-template-columns:repeat(4,1fr)}}
  .v-card{border-radius:18px;background:#0b3f60;overflow:hidden;border:1px solid rgba(255,255,255,.15);box-shadow:0 8px 24px rgba(0,0,0,.2)}
  .v-media{position:relative;height:170px;background:#133d57}
  .v-media img{width:100%;height:100%;object-fit:cover;display:block;filter:saturate(.95)}
  .v-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55),rgba(0,0,0,.1))}
  .v-badges{position:absolute;top:10px;left:10px;display:flex;gap6}
  .v-badge{background:rgba(255,255,255,.15);color:#fff;font-size:.75rem;border-radius:999px;padding:.2rem .55rem;backdrop-filter:blur(3px)}
  .v-body{background:#072c45;padding:14px}
  .v-title{font-weight:700;color:#fff;margin:0 0 .4rem 0;min-height:2.6em;line-height:1.3}
  .v-meta{font-size:.9rem;color:#d7e7f5;display:grid;gap:6px}
  .v-meta .row{display:flex;align-items:center;gap:8px}
  .v-meta .ico{width:18px;opacity:.9}
  .v-foot{display:flex;justify-content:space-between;align-items:center;padding:12px 14px;background:#06273c;border-top:1px solid rgba(255,255,255,.08)}
  .v-btn{background:#1081ff;border:none;color:#fff;border-radius:12px;padding:.4rem .9rem;font-weight:600}
  .v-btn:hover{filter:brightness(1.05)}
  .v-num{font-size:.85rem;color:#cde0f3}
  .text-muted-lite{color:#aac6da}
</style>

<div class="container my-4">
  <div class="vol-section">
    <div class="vol-head mb-2">Explore Opportunities</div>

    {{-- Small filter: All / On-site / Virtual --}}
    <div class="d-flex vol-tabs">
      <button class="btn btn-light btn-sm" data-filter="all">All</button>
      <button class="btn btn-outline-light btn-sm" data-filter="onsite">On-site</button>
      <button class="btn btn-outline-light btn-sm" data-filter="virtual">Virtual</button>
    </div>

    {{-- Opportunities grid --}}
    <div class="vol-grid" id="oppsGrid">
      @forelse(($opps ?? collect()) as $o)
        @php
          $isVirtual = false;
          $loc = strtolower(trim(($o->location ?? '') . ' ' . ($o->city ?? '')));
          if (str_contains($loc,'virtual') || str_contains($loc,'online') || str_contains($loc,'remote')) $isVirtual = true;
          $dateTxt = $o->date ? \Carbon\Carbon::parse($o->date)->format('M d, Y') : null;
        @endphp
        <div class="v-card" data-variant="{{ $isVirtual ? 'virtual':'onsite' }}">
          <div class="v-media">
            <div class="v-overlay"></div>
            <div class="v-badges">
              @if(!empty($o->category)) <span class="v-badge">{{ $o->category }}</span> @endif
              @if(!empty($o->region))   <span class="v-badge">{{ $o->region }}</span>   @endif
            </div>
          </div>
          <div class="v-body">
            <h6 class="v-title">{{ $o->title }}</h6>
            <div class="v-meta">
              @if($dateTxt)
                <div class="row"><svg class="ico" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2h2v2h6V2h2v2h3v18H4V4h3V2zm13 8H4v10h16V10z"/></svg><span>{{ $dateTxt }}</span></div>
              @endif
              @if($o->city || $o->location)
                <div class="row"><svg class="ico" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 0 1 7 7c0 5-7 13-7 13S5 14 5 9a7 7 0 0 1 7-7zm0 9.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/></svg>
                  <span>{{ $o->city ?? '' }} {{ $o->location ? '· '.$o->location : '' }}</span>
                </div>
              @endif
              @if($o->start_time || $o->end_time)
                <div class="row"><svg class="ico" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1a11 11 0 1 0 11 11A11.013 11.013 0 0 0 12 1Zm1 11h5v2h-7V6h2Z"/></svg>
                  <span>{{ $o->start_time ?? '' }} @if($o->end_time) – {{ $o->end_time }} @endif</span>
                </div>
              @endif
            </div>
          </div>
          <div class="v-foot">
            <div class="v-num">
              @if(!empty($o->slots)) <span class="text-muted-lite">{{ $o->slots }}</span> slots @endif
            </div>
            <a href="{{ url('/opportunities') }}" class="v-btn">Register</a>
          </div>
        </div>
      @empty
        <div class="text-center w-100 text-muted">No opportunities yet.</div>
      @endforelse
    </div>

    <hr class="my-4" style="border-color:rgba(255,255,255,.15)">

    <div class="vol-head mb-3">Latest Events</div>

    <div class="vol-grid">
      @forelse(($events ?? collect()) as $e)
        @php
          $eDate = $e->date ? \Carbon\Carbon::parse($e->date)->format('M d, Y') : null;
        @endphp
        <div class="v-card">
          <div class="v-media">
            @if(!empty($e->poster_path))
              <img src="{{ asset('storage/'.$e->poster_path) }}" alt="{{ $e->title }}">
            @endif
            <div class="v-overlay"></div>
            <div class="v-badges">
              @if(!empty($e->category)) <span class="v-badge">{{ $e->category }}</span> @endif
              @if(!empty($e->region))   <span class="v-badge">{{ $e->region }}</span>   @endif
            </div>
          </div>
          <div class="v-body">
            <h6 class="v-title">{{ $e->title }}</h6>
            <div class="v-meta">
              @if($eDate)
                <div class="row"><svg class="ico" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2h2v2h6V2h2v2h3v18H4V4h3V2zm13 8H4v10h16V10z"/></svg><span>{{ $eDate }}</span></div>
              @endif
              @if($e->city || $e->location)
                <div class="row"><svg class="ico" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 0 1 7 7c0 5-7 13-7 13S5 14 5 9a7 7 0 0 1 7-7zm0 9.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/></svg>
                  <span>{{ $e->city ?? '' }} {{ $e->location ? '· '.$e->location : '' }}</span>
                </div>
              @endif
              @if($e->start_time || $e->end_time)
                <div class="row"><svg class="ico" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1a11 11 0 1 0 11 11A11.013 11.013 0 0 0 12 1Zm1 11h5v2h-7V6h2Z"/></svg>
                  <span>{{ $e->start_time ?? '' }} @if($e->end_time) – {{ $e->end_time }} @endif</span>
                </div>
              @endif
            </div>
          </div>
          <div class="v-foot">
            <div class="v-num">
              @if(!empty($e->capacity)) <span class="text-muted-lite">{{ $e->capacity }}</span> seats @endif
            </div>
            <a href="{{ url('/events') }}" class="v-btn">View</a>
          </div>
        </div>
      @empty
        <div class="text-center w-100 text-muted">No events yet.</div>
      @endforelse
    </div>
  </div>
</div>

<script>
  // Simple client-side filter by "virtual" vs "onsite"
  (function(){
    const btns = document.querySelectorAll('[data-filter]');
    const grid = document.getElementById('oppsGrid');
    if(!grid) return;
    btns.forEach(b=>{
      b.addEventListener('click',()=>{
        const mode = b.getAttribute('data-filter');
        btns.forEach(x=>x.classList.remove('btn-light'));
        btns.forEach(x=>x.classList.add('btn-outline-light'));
        b.classList.remove('btn-outline-light');
        b.classList.add('btn-light');
        grid.querySelectorAll('.v-card').forEach(card=>{
          const v = card.getAttribute('data-variant');
          card.style.display = (mode==='all' || v===mode) ? '' : 'none';
        });
      });
    });
  })();
</script>
{{-- ===== END modern grid ===== --}}
