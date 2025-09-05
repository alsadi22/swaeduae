@extends('admin.layout')
@section('content')
<div class="container-fluid py-4">
  <h1 class="h5 mb-3">{{ __('KYC Reviews') }}</h1>
  @if(session('status')) <div class="alert alert-success">{{ session('status') }}</div> @endif
  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead><tr><th>{{ __('Org') }}</th><th>{{ __('Status') }}</th><th>{{ __('File') }}</th><th>{{ __('Submitted') }}</th><th></th></tr></thead>
        <tbody>
          @foreach($rows as $r)
            <tr>
              <td>{{ $r->org_name }}</td>
              <td>{{ ucfirst($r->status) }}</td>
              <td>@if($r->file_path)<a href="{{ $r->file_path }}" target="_blank">{{ __('View') }}</a>@endif</td>
              <td>{{ $r->submitted_at }}</td>
              <td class="text-end">
                <form method="POST" action="{{ route('admin.kyc.approve',$r->organization_id) }}" class="d-inline">@csrf
                  <button class="btn btn-sm btn-success">{{ __('Approve') }}</button>
                </form>
                <form method="POST" action="{{ route('admin.kyc.decline',$r->organization_id) }}" class="d-inline">@csrf
                  <input type="hidden" name="note" value="">
                  <button class="btn btn-sm btn-danger">{{ __('Decline') }}</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="card-body">{{ $rows->links() }}</div>
  </div>
</div>
@endsection
