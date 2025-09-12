@extends('admin.layout')
@section("title","Admin · Approvals")
@section("content")
@includeIf("admin._topbar")
<div class="container" style="margin-top:16px;">
  @if(session("status"))
    <div style="background:#ecfdf5;border:1px solid #10b98133;color:#065f46;padding:10px 12px;border-radius:10px;margin-bottom:10px;">Action: {{ session("status") }}</div>
  @endif
  @if(session("error"))
    <div style="background:#fef2f2;border:1px solid #ef444433;color:#991b1b;padding:10px 12px;border-radius:10px;margin-bottom:10px;">{{ session("error") }}</div>
  @endif
  <h2>Pending Organizations</h2>
  @php $hasRows = isset($pending) && $pending instanceof \Illuminate\Support\Collection && $pending->count() > 0; @endphp
  @if(!$hasRows)
    <div class="muted">No pending organizations (or table missing).</div>
  @else
  <div style="overflow:auto;margin-top:10px;">
    <table style="width:100%;border-collapse:collapse;">
      <thead><tr>
        <th style="text-align:left;padding:8px;border-bottom:1px solid #eee;">ID</th>
        <th style="text-align:left;padding:8px;border-bottom:1px solid #eee;">Name</th>
        <th style="text-align:left;padding:8px;border-bottom:1px solid #eee;">Email</th>
        <th style="text-align:left;padding:8px;border-bottom:1px solid #eee;">Created</th>
        <th style="padding:8px;border-bottom:1px solid #eee;">Actions</th>
      </tr></thead>
      <tbody>
      @foreach($pending as $row)
        <tr>
          <td style="padding:8px;border-bottom:1px solid #f3f3f3;">{{ $row->id ?? "" }}</td>
          <td style="padding:8px;border-bottom:1px solid #f3f3f3;">{{ $row->org_name ?? ($row->name ?? "—") }}</td>
          <td style="padding:8px;border-bottom:1px solid #f3f3f3;">{{ $row->email ?? "—" }}</td>
          <td style="padding:8px;border-bottom:1px solid #f3f3f3;">{{ $row->created_at ?? "—" }}</td>
          <td style="padding:8px;border-bottom:1px solid #f3f3f3;white-space:nowrap;">
            <form method="POST" action="{{ route("admin.approvals.orgs.approve",["id"=>$row->id]) }}" style="display:inline">@csrf
              <button class="btn" style="padding:6px 10px;border-radius:10px;border:1px solid #111;background:#111;color:#fff;">Approve</button>
            </form>
            <form method="POST" action="{{ route("admin.approvals.orgs.reject",["id"=>$row->id]) }}" style="display:inline">@csrf
              <button class="btn outline" style="padding:6px 10px;border-radius:10px;border:1px solid #111;background:#fff;">Reject</button>
            </form>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>
@endsection
