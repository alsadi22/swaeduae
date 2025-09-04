<!doctype html><html><head><meta charset="utf-8"><title>My Hours</title>
<style>body{font-family:system-ui,Arial;margin:2rem} table{border-collapse:collapse} td,th{padding:.5rem 1rem;border:1px solid #eee}</style></head>
<body>
  <h2>My Volunteer Hours</h2>
  <table>
    <thead><tr><th>Date</th><th>Opportunity</th><th>Hours</th><th>Notes</th></tr></thead>
    <tbody>
      @forelse($rows as $r)
        <tr>
          <td>{{ $r->date }}</td>
          <td>{{ $r->opportunity_id }}</td>
          <td>{{ $r->hours }}</td>
          <td>{{ $r->notes }}</td>
        </tr>
      @empty
        <tr><td colspan="4">No records.</td></tr>
      @endforelse
    </tbody>
  </table>
</body></html>
