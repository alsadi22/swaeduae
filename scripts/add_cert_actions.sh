#!/usr/bin/env bash
set -euo pipefail
TS="$(date +%Y%m%d_%H%M%S)"

echo "==> Backups"
cp -f resources/views/volunteer/profile.blade.php "resources/views/volunteer/profile.blade.php.$TS.bak" || true
cp -f resources/views/admin/attendance/index.blade.php "resources/views/admin/attendance/index.blade.php.$TS.bak" || true
cp -f resources/lang/en/messages.php "resources/lang/en/messages.php.$TS.bak" || true
cp -f resources/lang/ar/messages.php "resources/lang/ar/messages.php.$TS.bak" || true

echo "==> Update Volunteer Certificates table with WhatsApp share"
cat > resources/views/volunteer/profile.blade.php <<'BLADE'
@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1 class="mb-3">{{ __('messages.my_profile') }}</h1>

  <div class="row mb-3">
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">{{ $user->name }}</h5>
          <p class="mb-1">{{ $user->email }}</p>
          <p class="mb-0">{{ __('messages.total_hours') }}: <strong>{{ $totalHours }}</strong></p>
        </div>
      </div>
    </div>
  </div>

  <ul class="nav nav-tabs mb-3">
    @php $tabs = ['overview'=>__('messages.overview'), 'hours'=>__('messages.hours'), 'events'=>__('messages.events_attended'), 'applications'=>__('messages.applications'), 'certificates'=>__('messages.certificates')]; @endphp
    @foreach($tabs as $key=>$label)
      <li class="nav-item">
        <a class="nav-link {{ $tab === $key ? 'active' : '' }}" href="{{ route('volunteer.profile', ['tab'=>$key]) }}">{{ $label }}</a>
      </li>
    @endforeach
  </ul>

  @if($tab === 'overview' || $tab === 'hours')
    <div class="card mb-3">
      <div class="card-header">{{ __('messages.hours') }}</div>
      <div class="card-body">
        <p>{{ __('messages.total_hours') }}: <strong>{{ $totalHours }}</strong></p>
      </div>
    </div>
  @endif

  @if($tab === 'overview' || $tab === 'events')
    <div class="card mb-3">
      <div class="card-header">{{ __('messages.events_attended') }}</div>
      <div class="card-body table-responsive">
        <table class="table table-sm">
          <thead><tr><th>#</th><th>{{ __('messages.event') }}</th><th>{{ __('messages.minutes') }}</th><th>{{ __('messages.checked_in') }}</th><th>{{ __('messages.checked_out') }}</th></tr></thead>
          <tbody>
            @foreach($events as $e)
              <tr>
                <td>{{ $e->id }}</td>
                <td>{{ $e->opportunity?->title }}</td>
                <td>{{ $e->minutes }}</td>
                <td>{{ $e->check_in_at }}</td>
                <td>{{ $e->check_out_at }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
        {{ $events->links() }}
      </div>
    </div>
  @endif

  @if($tab === 'overview' || $tab === 'applications')
    <div class="card mb-3">
      <div class="card-header">{{ __('messages.applications') }}</div>
      <div class="card-body table-responsive">
        <table class="table table-sm">
          <thead><tr><th>#</th><th>{{ __('messages.event') }}</th><th>{{ __('messages.status') }}</th><th>{{ __('messages.applied_at') }}</th></tr></thead>
          <tbody>
            @foreach($applications as $a)
              <tr>
                <td>{{ $a->id }}</td>
                <td>{{ $a->opportunity_id }}</td>
                <td>{{ $a->status }}</td>
                <td>{{ $a->created_at }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
        {{ $applications->links() }}
      </div>
    </div>
  @endif

  @if($tab === 'overview' || $tab === 'certificates')
    <div class="card mb-3">
      <div class="card-header">{{ __('messages.certificates') }}</div>
      <div class="card-body table-responsive">
        <table class="table table-sm">
          <thead><tr><th>#</th><th>{{ __('messages.event') }}</th><th>{{ __('messages.hours') }}</th><th>{{ __('messages.issued_at') }}</th><th>{{ __('messages.actions') }}</th></tr></thead>
          <tbody>
            @foreach($certificates as $c)
              @php
                $downloadUrl = url('/storage/'.$c->file_path);
                $verifyUrl = url('/verify/'.$c->code);
                $whatsMsg = "السلام عليكم {$user->name}،\nشهادتك التطوعية جاهزة ✅\nتنزيل: {$downloadUrl}\nالتحقق: {$verifyUrl}\nشكراً لمساهمتك!";
                $wa = 'https://wa.me/?text='.urlencode($whatsMsg);
              @endphp
              <tr>
                <td>{{ $c->id }}</td>
                <td>{{ $c->event?->title }}</td>
                <td>{{ $c->hours }}</td>
                <td>{{ $c->issued_at }}</td>
                <td class="d-flex gap-1">
                  <a class="btn btn-sm btn-outline-primary" href="{{ $downloadUrl }}" target="_blank">{{ __('messages.download') }}</a>
                  <a class="btn btn-sm btn-outline-secondary" href="{{ $verifyUrl }}" target="_blank">{{ __('messages.verify') }}</a>
                  <a class="btn btn-sm btn-outline-success" href="{{ $wa }}" target="_blank">{{ __('messages.share_whatsapp') }}</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        {{ $certificates->links() }}
      </div>
    </div>
  @endif

</div>
@endsection
BLADE

echo "==> Update Admin Attendance Manager with Resend Email button (if certificate exists)"
cat > resources/views/admin/attendance/index.blade.php <<'BLADE'
@extends('admin.layout')

@section('content')
<div class="container-fluid">
  <h1 class="mt-3 mb-3">{{ __('messages.attendance_manager') }} — {{ $opportunity->title }}</h1>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="mb-3">
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
                <div class="d-flex gap-1">
                  <button class="btn btn-sm btn-success">{{ __('messages.save') }}</button>
                  @if($cert)
                    <form method="POST" action="{{ route('admin.certificates.resend', $cert) }}" onsubmit="return confirm('{{ __('messages.confirm_resend_email') }}')">
                      @csrf
                      <button class="btn btn-sm btn-outline-primary">{{ __('messages.resend_email') }}</button>
                    </form>
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

echo "==> Add i18n keys"
# EN
printf "\n<?php if (!isset(\$messages)) \$messages = []; return array_replace_recursive(\$messages ?? [], [ 'share_whatsapp' => 'Share via WhatsApp', 'resend_email' => 'Resend email', 'confirm_resend_email' => 'Resend this certificate email now?', ]);\n" >> resources/lang/en/messages.php
# AR
printf "\n<?php if (!isset(\$messages)) \$messages = []; return array_replace_recursive(\$messages ?? [], [ 'share_whatsapp' => 'مشاركة عبر واتساب', 'resend_email' => 'إعادة إرسال البريد', 'confirm_resend_email' => 'هل تريد إعادة إرسال البريد الآن؟', ]);\n" >> resources/lang/ar/messages.php

echo "==> Clear caches"
php artisan view:clear && php artisan route:clear && php artisan config:clear && php artisan cache:clear
php artisan config:cache && php artisan route:cache

echo "==> Done. Backups at *.$TS.bak"
