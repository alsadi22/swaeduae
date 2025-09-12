<footer class="footer mt-5 pt-4">
  <div class="container">

    {{-- Top CTA / app badges --}}
    <div class="row gy-3 align-items-center pb-4">
      <div class="col-lg-6">
        <div class="brand fs-4">SawaedUAE</div>
        <div class="muted small">Volunteering for everyone — connect with organizations, join events, and track your impact.</div>
      </div>
      <div class="col-lg-6 text-lg-end app-badges">
        <a class="btn btn-sm me-2" href="#" title="App Store (coming soon)"><i class="bi bi-apple me-1"></i> App Store</a>
        <a class="btn btn-sm" href="#" title="Google Play (coming soon)"><i class="bi bi-google-play me-1"></i> Google Play</a>
      </div>
    </div>

    <div class="divider mb-4"></div>

    {{-- Main columns --}}
    <div class="row gy-4">
      <div class="col-md-4">
        <div class="heading">About</div>
        <p class="muted">
          SawaedUAE is a community platform that makes volunteering simple and meaningful.
          Discover opportunities, apply in seconds, and build your contribution record.
        </p>

        <div class="social mt-2">
          <a href="#" aria-label="Twitter"><i class="bi bi-twitter"></i></a>
          <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
          <a href="mailto:{{ \App\Models\Setting::get('contact.email','info@swaeduae.ae') }}" aria-label="Email"><i class="bi bi-envelope"></i></a>
        </div>

        <div class="mt-3 ctrls">
          <span class="chip me-2"><i class="bi bi-telephone me-1"></i> {{ \App\Models\Setting::get('contact.phone','800-VOLA E (86523)') }}</span>
          <span class="chip"><i class="bi bi-geo-alt me-1"></i> UAE</span>
        </div>
      </div>

      <div class="col-6 col-md-2">
        <div class="heading">Explore</div>
        <div class="link-list">
          <a href="{{ route('opportunities.index') }}">Opportunities</a>
          <a href="{{ route('events.index') }}">Events</a>
          <a href="{{ url('/partners') }}">Organizations</a>
          <a href="{{ url('/') }}">Gallery</a>
        </div>
      </div>

      <div class="col-6 col-md-2">
        <div class="heading">Help</div>
        <div class="link-list">
          <a href="#">FAQ</a>
          <a href="#">Terms</a>
          <a href="#">Privacy Policy</a>
          <a href="/contact">Contact</a>
        </div>
      </div>

      <div class="col-md-4">
        <div class="heading">Stay in the loop</div>
        <form class="row g-2" action="#" method="post" onsubmit="event.preventDefault(); alert('Thanks! (wire to Mail/PHP later)');">
    @csrf
          <div class="col-8">
            <input type="email" class="form-control form-control-sm" placeholder="Your email" required>
          </div>
          <div class="col-4 d-grid">
            <button class="btn btn-sm" style="background:var(--rose); color:#132027; border:0;">Subscribe</button>
          </div>
        </form>

        <div class="mt-3 ctrls">
          <span class="muted me-2">Text size:</span>
          <button type="button" class="btn btn-sm me-1" onclick="__ftSize(-1)">A–</button>
          <button type="button" class="btn btn-sm me-1" onclick="__ftSize(0)">A</button>
          <button type="button" class="btn btn-sm" onclick="__ftSize(1)">A+</button>
        </div>
      </div>
    </div>

    <div class="divider my-4"></div>

    {{-- Bottom bar --}}
    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between pb-3">
      <div class="muted small">
        © <span id="yr"></span> SawaedUAE. All rights reserved.
      </div>
      <div class="muted small">
        Built with ❤️ for volunteers across the UAE.
      </div>
    </div>
  </div>
</footer>

<button id="backToTop" title="Back to top"><i class="bi bi-arrow-up"></i></button>

@push('scripts')
<script>
  // Year
  document.getElementById('yr').textContent = new Date().getFullYear();

  // Back-to-top
  const btt = document.getElementById('backToTop');
  window.addEventListener('scroll', ()=>{ btt.style.display = (window.scrollY>400)?'inline-flex':'none'; });
  btt.addEventListener('click', ()=>window.scrollTo({top:0, behavior:'smooth'}));

  // Simple font-size control (stores in localStorage)
  const base = 100; // 100% default
  function setPct(p){ document.documentElement.style.fontSize = p + '%'; localStorage.setItem('fontPct', p); }
  window.__ftSize = function(dir){
    if(dir===0){ setPct(base); return; }
    const cur = parseInt(getComputedStyle(document.documentElement).fontSize) / 16 * 100;
    const next = Math.max(87, Math.min(115, cur + (dir>0?5:-5)));
    setPct(next);
  };
  (function(){
    const saved = parseInt(localStorage.getItem('fontPct'));
    if(saved && saved!==base){ setPct(saved); }
  })();
</script>
@endpush
