#!/usr/bin/env bash
set -euo pipefail
TS="$(date +%Y%m%d_%H%M%S)"

echo "==> Backup admin attendance view"
cp -f resources/views/admin/attendance/index.blade.php "resources/views/admin/attendance/index.blade.php.$TS.bak" || true

cat > resources/views/admin/attendance/index.blade.php <<'BLADE'
@extends('admin.layout')

@section('content')
<div class="container-fluid">
  <h1 class="mt-3 mb-3">{{ __('messages.attendance_manager') }} — {{ $opportunity->title }}</h1>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="mb-3 d-flex gap-2">
    <form action="{{ route('admin.opportunities.finalize.issue', $opportunity) }}" method="POST" onsubmit="return confirm('{{ __('messages.issue_confirm') }}')">
      @csrf
      <button class="btn btn-primary">{{ __('messages.issue_certificates_now') }}</button>
    </form>
  </div>

  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <th>#</th>
          <th>{{ __('messages.volunteer') }}</th>
          <th>{{ __('messages.check_in') }}</th>
          <th>{{ __('messages.check_out') }}</th>
          <th>{{ __('messages.minutes') }}</th>
          <th>{{ __('messages.no_show') }}</th>
          <th>{{ __('messages.notes') }}</th>
          <th>{{ __('messages.actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($attendances as $a)
          @php
            $cert = \App\Models\Certificate::where('user_id', $a->user_id)->where('event_id', $opportunity->id)->first();
            $downloadUrl = $cert ? url('/storage/'.$cert->file_path) : null;
            $verifyUrl   = $cert ? url('/verify/'.$cert->code) : null;
            $wa = $cert ? ('https://wa.me/?text='.urlencode(
              "السلام عليكم {$a->user?->name}،\nشهادتك التطوعية جاهزة ✅\nتنزيل: {$downloadUrl}\nالتحقق: {$verifyUrl}\nشكراً لمساهمتك!"
            )) : null;
          @endphp
          <tr>
            <td>{{ $a->id }}</td>
            <td>{{ $a->user?->name }}</td>
            <form method="POST" action="{{ route('admin.opportunities.attendance.update', [$opportunity, $a]) }}">
              @csrf
              <td><input type="datetime-local" name="check_in_at" class="form-control form-control-sm" value="{{ $a->check_in_at ? $a->check_in_at->format('Y-m-d\TH:i') : '' }}"></td>
              <td><input type="datetime-local" name="check_out_at" class="form-control form-control-sm" value="{{ $a->check_out_at ? $a->check_out_at->format('Y-m-d\TH:i') : '' }}"></td>
              <td style="max-width:120px"><input type="number" name="minutes" class="form-control form-control-sm" min="0" value="{{ $a->minutes }}"></td>
              <td><input type="checkbox" name="no_show" value="1" {{ $a->no_show ? 'checked' : '' }}></td>
              <td><input type="text" name="notes" class="form-control form-control-sm" value="{{ $a->notes }}"></td>
              <td>
                <div class="d-flex flex-wrap gap-1">
                  <button class="btn btn-sm btn-success">{{ __('messages.save') }}</button>
                  @if($cert)
                    <form method="POST" action="{{ route('admin.certificates.resend', $cert) }}" onsubmit="return confirm('{{ __('messages.confirm_resend_email') }}')">
                      @csrf
                      <button class="btn btn-sm btn-outline-primary">{{ __('messages.resend_email') }}</button>
                    </form>
                    <a class="btn btn-sm btn-outline-success" href="{{ $wa }}" target="_blank">{{ __('messages.share_whatsapp') }}</a>
                  @endif
                </div>
              </td>
            </form>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{ $attendances->links() }}
</div>
@endsection
BLADE

echo "==> Clear caches"
php artisan view:clear && php artisan route:clear && php artisan config:clear && php artisan cache:clear
php artisan config:cache && php artisan route:cache

echo "==> Done. Backup at resources/views/admin/attendance/index.blade.php.$TS.bak"
