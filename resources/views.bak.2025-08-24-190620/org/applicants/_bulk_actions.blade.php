<form method="POST" action="{{ route('org.applicants.decision.bulk') }}" class="card shadow-sm mb-3">
  @csrf
  <div class="card-body d-flex flex-wrap align-items-center gap-2">
    <input type="hidden" name="action" id="bulk-action">
    <input type="text" name="note" class="form-control" placeholder="{{ __('Optional note to include in email') }}" style="min-width:240px;max-width:420px">
    <button type="button" class="btn btn-success"  onclick="setBulkAction('approved')">{{ __('Approve') }}</button>
    <button type="button" class="btn btn-warning"  onclick="setBulkAction('waitlist')">{{ __('Waitlist') }}</button>
    <button type="button" class="btn btn-danger"   onclick="setBulkAction('declined')">{{ __('Decline') }}</button>
    @if (session('status')) <span class="ms-2 text-success">{{ session('status') }}</span> @endif
    @error('application_ids') <span class="ms-2 text-danger">{{ $message }}</span> @enderror
  </div>
</form>
<script>
function setBulkAction(act){
  const ids = document.querySelectorAll('input[name="application_ids[]"]:checked');
  if(!ids.length){ alert('{{ __('Select at least one application') }}'); return; }
  document.getElementById('bulk-action').value = act;
  // Create hidden inputs for selected IDs if not already present inside the form
  const form = event.target.closest('form');
  // Remove old hiddens
  form.querySelectorAll('input[type="hidden"][name="application_ids[]"]').forEach(e=>e.remove());
  ids.forEach(chk => {
    const hid = document.createElement('input');
    hid.type = 'hidden';
    hid.name = 'application_ids[]';
    hid.value = chk.value;
    form.appendChild(hid);
  });
  form.submit();
}
</script>

<script>
async function setBulkAction(act){
  const ids = document.querySelectorAll('input[name="application_ids[]"]:checked');
  if(!ids.length){ alert('{{ __('Select at least one application') }}'); return; }
  const form = event.target.closest('form');
  document.getElementById('bulk-action').value = act;

  // Prepare payload
  const payload = new FormData(form);
  // Reset previous hidden ids
  form.querySelectorAll('input[type="hidden"][name="application_ids[]"]').forEach(e=>e.remove());
  ids.forEach(chk => payload.append('application_ids[]', chk.value));

  try {
    const res = await fetch(form.action, {
      method: 'POST',
      headers: {'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value},
      body: payload
    });
    const j = await res.json();
    if (j.status) {
      const statusEl = document.createElement('span');
      statusEl.className = 'ms-2 text-success'; statusEl.textContent = j.status;
      form.querySelector('.card-body').appendChild(statusEl);
    }
    if (j.summary) updateShortlistCounters(j.summary);
  } catch(e){
    form.submit(); // fallback non-AJAX
  }
}

function updateShortlistCounters(summary){
  // We update visible counter cards if present (requires our _counters partial on page)
  Object.keys(summary).forEach(oppId => {
    const s = summary[oppId];
    // You can enhance by tagging elements with data-opp and data-kind to be precise.
    // For now, naive: update text of obvious cards if they exist.
    document.querySelectorAll('.card .card-body').forEach(div=>{
      if (div.textContent.match(/Shortlisted/i)) div.querySelector('.h4,.h3')?.innerText = s.shortlisted;
      if (div.textContent.match(/Approved/i)) div.querySelector('.h4,.h3')?.innerText = s.approved;
      if (div.textContent.match(/Pending/i)) div.querySelector('.h4,.h3')?.innerText = s.pending;
      if (div.textContent.match(/Slot Cap/i)) div.querySelector('.h4,.h3')?.innerText = (s.cap ?? '—');
    });
  });
}
</script>
