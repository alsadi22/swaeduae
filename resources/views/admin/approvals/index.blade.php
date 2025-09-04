@extends('admin.layout')
@section('content')
<div class="container-fluid py-3">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0">Approvals</h4>
    <form class="d-flex gap-2" method="get" action="{{ route('admin.approvals.index') }}">
      @php($t = request('type','all')); @php($s = request('status','pending'))
      <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
        <option value="all"  {{ $t==='all'?'selected':'' }}>All</option>
        <option value="orgs" {{ $t==='orgs'?'selected':'' }}>Organizations</option>
        <option value="apps" {{ $t==='apps'?'selected':'' }}>Applications</option>
      </select>
      <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
        @foreach(['pending','approved','denied'] as $opt)
          <option value="{{ $opt }}" {{ $s===$opt?'selected':'' }}>{{ ucfirst($opt) }}</option>
        @endforeach
      </select>
    </form>
  </div>

  @foreach([['orgs',$orgs,'Organizations'],['apps',$apps,'Volunteer Applications']] as [$key,$rows,$title])
    @if($type==='all' || $type===$key)
    <div class="card mb-4">
      <div class="card-header"><strong>{{ $title }}</strong></div>
      <div class="table-responsive">
        <table class="table table-sm mb-0">
          <thead><tr><th>ID</th><th>A</th><th>B</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
          <tbody>
          @forelse($rows as $r)
            <tr>
              <td>{{ $r->id }}</td>
              <td>{{ $r->org_name ?? $r->user_id ?? '-' }}</td>
              <td>{{ $r->org_code ?? $r->opportunity_id ?? '-' }}</td>
              <td><span class="badge bg-secondary">{{ $r->status }}</span></td>
              <td>{{ \Carbon\Carbon::parse($r->created_at)->diffForHumans() }}</td>
              <td class="d-flex gap-2">
                @if($key==='orgs')
                  <form method="post" action="{{ route('admin.approvals.orgs.approve',$r->id) }}">@csrf
                    <button class="btn btn-success btn-sm">Approve</button>
                  </form>
                  <form method="post" action="{{ route('admin.approvals.orgs.deny',$r->id) }}" class="d-flex gap-1">@csrf
                    <input name="reason" class="form-control form-control-sm" placeholder="Reason" />
                    <button class="btn btn-danger btn-sm">Deny</button>
                  </form>
                @else
                  <form method="post" action="{{ route('admin.approvals.apps.approve',$r->id) }}">@csrf
                    <button class="btn btn-success btn-sm">Approve</button>
                  </form>
                  <form method="post" action="{{ route('admin.approvals.apps.deny',$r->id) }}" class="d-flex gap-1">@csrf
                    <input name="reason" class="form-control form-control-sm" placeholder="Reason" />
                    <button class="btn btn-danger btn-sm">Deny</button>
                  </form>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-muted">No records.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @endif
  @endforeach
</div>
@endsection
