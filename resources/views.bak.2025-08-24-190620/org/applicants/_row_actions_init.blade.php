<script>
document.addEventListener('DOMContentLoaded', function() {
  const table = document.querySelector('table');
  if(!table) return;

  // Add header cell "Actions" if missing
  const theadRow = table.tHead && table.tHead.rows[0];
  if (theadRow && !Array.from(theadRow.cells).some(th => /Actions|الإجراءات/i.test(th.textContent))) {
    const th = document.createElement('th');
    th.textContent = '{{ __("Actions") }}';
    theadRow.appendChild(th);
  }

  // For each row with application checkbox, append action cell
  const rows = table.tBodies.length ? table.tBodies[0].rows : [];
  Array.from(rows).forEach(tr => {
    const cb = tr.querySelector('input[type="checkbox"][name="application_ids[]"]');
    if (!cb || !cb.value) return;
    const appId = cb.value;

    // Skip if already added
    if (tr.querySelector('.app-actions-cell')) return;

    const td = document.createElement('td');
    td.className = 'app-actions-cell';
    td.innerHTML = `
      <form method="POST" action="{{ route('org.applicants.decision.single', ['application' => 'APPID']) }}" class="d-inline">
        @csrf
        <input type="hidden" name="action" value="approved">
        <button class="btn btn-sm btn-success">{{ __('Approve') }}</button>
      </form>
      <form method="POST" action="{{ route('org.applicants.decision.single', ['application' => 'APPID']) }}" class="d-inline ms-1">
        @csrf
        <input type="hidden" name="action" value="waitlist">
        <button class="btn btn-sm btn-warning">{{ __('Waitlist') }}</button>
      </form>
      <form method="POST" action="{{ route('org.applicants.decision.single', ['application' => 'APPID']) }}" class="d-inline ms-1">
        @csrf
        <input type="hidden" name="action" value="declined">
        <button class="btn btn-sm btn-danger">{{ __('Decline') }}</button>
      </form>
    `.replaceAll('APPID', appId);
    tr.appendChild(td);
  });
});
</script>
