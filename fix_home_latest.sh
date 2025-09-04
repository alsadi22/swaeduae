#!/usr/bin/env bash
set -euo pipefail
cd /home3/vminingc/swaeduae.ae/laravel-app

HOME_VIEW="resources/views/home.blade.php"

# Backup
cp -a "$HOME_VIEW"{,.bak.$(date +%F-%H%M)}

# Replace the PHOTO STRIP block with a mixed latest feed
perl -0777 -i -pe '
  s{
    <!--\s*PHOTO\s+STRIP.*?-->    # anchor comment
    .*?                           # existing grid
    </div>\s*                     # end .row
    </div>\s*                     # end .container
  }{
    <<\'BLADE\'
    <!-- LATEST OPPORTUNITIES & EVENTS (mixed, newest first) -->
    </div>  {{-- close the hero container opened above --}}

    <div class="container my-4 my-md-5">
      @php
        // Collect candidates (defensive: pick best available timestamp)
        $ops = \App\Models\Opportunity::query()
          ->orderByDesc("created_at")->take(8)->get()
          ->map(function($o){
            $o->kind = "op";
            $o->when = $o->starts_at ?? $o->issued_at ?? $o->created_at;
            return $o;
          });

        $evs = \App\Models\Event::query()
          ->orderByDesc("created_at")->take(8)->get()
          ->map(function($e){
            $e->kind = "ev";
            $e->when = $e->date ?? $e->starts_at ?? $e->created_at;
            return $e;
          });

        $latest = $ops->concat($evs)->sortByDesc(function($x){
          return \Illuminate\Support\Carbon::parse($x->when ?? $x->created_at);
        })->take(6);
      @endphp

      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 m-0">{{ __("Latest Opportunities & Events") }}</h2>
        <div class="d-none d-md-flex gap-2">
          <a class="btn btn-sm btn-outline-primary" href="{{ url("/opportunities") }}">{{ __("All opportunities") }}</a>
          <a class="btn btn-sm btn-outline-secondary" href="{{ url("/events") }}">{{ __("All events") }}</a>
        </div>
      </div>

      <div class="row g-3">
        @forelse($latest as $i)
          <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="badge bg-{{ $i->kind === "op" ? "primary" : "info" }}">
                    {{ $i->kind === "op" ? __("Opportunity") : __("Event") }}
                  </span>
                  @if(!empty($i->region))
                    <span class="text-muted small">{{ $i->region }}</span>
                  @endif
                </div>

                <h5 class="card-title mb-1 text-truncate">{{ $i->title ?? $i->name ?? ("#".$i->id) }}</h5>

                <div class="text-muted small mb-2">
                  @if(!empty($i->when))
                    {{ \Illuminate\Support\Carbon::parse($i->when)->format("d M Y") }}
                  @endif
                  @if(!empty($i->category))
                    &nbsp;•&nbsp; {{ $i->category }}
                  @endif
                </div>

                <p class="card-text small text-muted" style="min-height:3em">
                  {{ \Illuminate\Support\Str::limit(strip_tags($i->description ?? $i->details ?? ""), 140) }}
                </p>

                <div class="d-flex gap-2">
                  @if($i->kind === "op")
                    <a href="{{ url("/opportunities") }}" class="btn btn-outline-primary btn-sm">
                      {{ __("View Details & Apply") }}
                    </a>
                  @else
                    <a href="{{ url("/events") }}" class="btn btn-outline-secondary btn-sm">
                      {{ __("View Event") }}
                    </a>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12">
            <div class="alert alert-light m-0">{{ __("No items yet.") }}</div>
          </div>
        @endforelse
      </div>
    </div>
    BLADE
  }gsx
' "$HOME_VIEW"

php artisan view:clear >/dev/null
echo "Updated $HOME_VIEW and cleared compiled views."
