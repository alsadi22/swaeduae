@extends('org.layout')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">{{ __('Attendance Scanner') }}</h1>
    <a href="{{ route('org.dashboard') }}" class="btn btn-outline-secondary">{{ __('Back to Dashboard') }}</a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <p class="mb-2"><strong>{{ __('Opportunity') }}:</strong> {{ $opportunity->title ?? ('#'.$opportunity->id) }}</p>

      <div class="mb-3">
        <label class="form-label">{{ __('Mode') }}</label>
        <div class="btn-group" role="group">
          <input type="radio" class="btn-check" name="mode" id="mode-in" autocomplete="off" checked>
          <label class="btn btn-sm btn-outline-primary" for="mode-in">{{ __('Check-in') }}</label>

          <input type="radio" class="btn-check" name="mode" id="mode-out" autocomplete="off">
          <label class="btn btn-sm btn-outline-primary" for="mode-out">{{ __('Check-out') }}</label>
        </div>
      </div>

      <div id="scanner" class="border rounded mb-3" style="width:100%;max-width:480px;min-height:300px;"></div>

      <div class="mb-2 text-muted" id="scan-status">{{ __('Camera initializing...') }}</div>

      <div class="mb-3">
        <label class="form-label">{{ __('Manual code (fallback)') }}</label>
        <div class="input-group">
          <input type="text" id="manual-code" class="form-control" placeholder="{{ __('Paste QR code text or user ID...') }}">
          <button class="btn btn-primary" id="btn-submit-manual">{{ __('Submit') }}</button>
        </div>
      </div>

      <div id="queue-status" class="small text-muted mb-2"></div>
      <div id="toast" class="alert d-none" role="alert"></div>
    </div>
  </div>
</div>

<script src="https://unpkg.com/html5-qrcode" defer></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const oppId = {{ (int)$opportunity->id }};
  const csrf = @json($csrf);
  const elScanner = document.getElementById('scanner');
  const elStatus  = document.getElementById('scan-status');
  const elToast   = document.getElementById('toast');
  const elQueue   = document.getElementById('queue-status');
  const modeIn    = document.getElementById('mode-in');
  const modeOut   = document.getElementById('mode-out');

  const QKEY = 'swaed:scanQueue';

  function toast(kind, msg) {
    elToast.className = 'alert alert-' + kind;
    elToast.textContent = msg;
    elToast.classList.remove('d-none');
    setTimeout(() => elToast.classList.add('d-none'), 2500);
  }

  function vibrate(ms=60){ if (navigator.vibrate) try { navigator.vibrate(ms); } catch(e){} }

  function currentAction() { return modeOut.checked ? 'checkout' : 'checkin'; }

  function readQueue(){
    try { return JSON.parse(localStorage.getItem(QKEY) || '[]'); } catch(e){ return []; }
  }
  function writeQueue(q){ localStorage.setItem(QKEY, JSON.stringify(q)); updateQueueBadge(); }
  function pushQueue(item){ const q = readQueue(); q.push(item); writeQueue(q); }
  function updateQueueBadge(){
    const n = readQueue().length;
    elQueue.textContent = n ? (`{{ __('Pending offline scans') }}: ${n}`) : '';
  }

  async function submitPayload(payload, opts={}) {
    const action = opts.action || currentAction();
    const url = `/org/attendance/${oppId}/${action}`;
    const body = JSON.stringify({ qr: payload });
    navigator.geolocation.getCurrentPosition(function(pos){ bodyObj={ qr: payload, lat: pos.coords.latitude, lng: pos.coords.longitude, acc: pos.coords.accuracy }; doSubmit(bodyObj); }, function(){ doSubmit({ qr: payload }); }, {enableHighAccuracy:true,timeout:3000}); return;

    try {
      const res = await fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/json','X-CSRF-TOKEN': csrf, 'Accept': 'application/json'},
        body
      });
      const j = await res.json();
      if (j.ok) {
        toast('success', j.message || 'OK');
        vibrate(35);
        return true;
      } else {
        toast('danger', j.message || 'Failed');
        return false;
      }
    } catch (e) {
      // Offline or network error — queue it
      pushQueue({ oppId, action, payload, ts: Date.now() });
      toast('warning', '{{ __('Offline — saved to queue') }}');
      return false;
    }
  }

  async function flushQueue(){
    const q = readQueue();
    if (!q.length) return;
    let okCount = 0, failRemain = [];
    for (const item of q) {
      const ok = await submitPayload(item.payload, {action:item.action});
      if (ok) okCount++; else failRemain.push(item);
    }
    writeQueue(failRemain);
    if (okCount) toast('success', `{{ __('Flushed') }}: ${okCount}`);
  }

  // Manual submit
  document.getElementById('btn-submit-manual').addEventListener('click', () => {
    const v = document.getElementById('manual-code').value.trim();
    if (!v) return toast('warning', '{{ __('Enter a code') }}');
    submitPayload(v);
  });

  // Camera scanner
  function initScanner() {
    if (!window.Html5Qrcode) {
      elStatus.textContent = '{{ __('Scanner library not loaded.') }}';
      return;
    }
    const scanner = new Html5Qrcode("scanner");
    const config = { fps: 10, qrbox: {width: 250, height: 250} };
    scanner.start({ facingMode: "environment" }, config,
      (decodedText) => {
        elStatus.textContent = '{{ __('Scanned') }}: ' + decodedText.substring(0, 64);
        submitPayload(decodedText);
      },
      (errMsg) => { /* mute */ }
    ).then(() => {
      elStatus.textContent = '{{ __('Camera ready. Point at QR.') }}';
    }).catch(err => {
      elStatus.textContent = '{{ __('Camera error. Use manual code.') }}';
    });
  }

  updateQueueBadge();
  setTimeout(initScanner, 400);

  // Flush queued scans when back online
  window.addEventListener('online', flushQueue);
  // Try to flush soon after page load
  setTimeout(flushQueue, 1000);
});
</script>
@endsection
<script>
async function doSubmit(obj){
  const oppId = {{ (int)$opportunity->id }};
  const csrf = @json($csrf);
  const action = document.getElementById('mode-out').checked ? 'checkout' : 'checkin';
  try{
    const res = await fetch(`/org/attendance/${oppId}/${action}`,{
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},
      body: JSON.stringify(obj)
    });
    const j = await res.json();
    const kind = j.ok ? 'success' : (res.status===429?'warning':'danger');
    toast(kind, j.message || (j.ok?'OK':'Failed'));
  }catch(e){ toast('warning','{{ __('Offline — saved to queue') }}'); }
}
</script>
