<script>
document.addEventListener('DOMContentLoaded', function(){
  const table = document.querySelector('table');
  if(!table) return;

  // Try to infer opportunity ID per row from links like /org/opportunities/{id}/edit or /org/opportunities/{id}
  const rows = table.tBodies.length ? table.tBodies[0].rows : [];
  Array.from(rows).forEach(tr => {
    if (tr.querySelector('.opp-cert-cell')) return;

    let id = null;
    const link = tr.querySelector('a[href*="/org/opportunities/"]');
    if (link) {
      const m = link.getAttribute('href').match(/\/org\/opportunities\/(\d+)/);
      if (m) id = m[1];
    }
    if (!id) return;

    const td = document.createElement('td');
    td.className = 'opp-cert-cell';
    td.innerHTML = `<a class="btn btn-sm btn-outline-primary" href="/org/opportunities/${id}/certificates">{{ __('Certificates') }}</a>`;

    // Append at the end; if an Actions column exists, append after it
    tr.appendChild(td);

    // Ensure header has an extra cell only once
    const head = table.tHead && table.tHead.rows[0];
    if (head && !Array.from(head.cells).some(c => /Certificates/i.test(c.textContent))) {
      const th = document.createElement('th');
      th.textContent = '{{ __("Certificates") }}';
      head.appendChild(th);
    }
  });
});
</script>
