@extends('layouts.app')

@section('title', __('Certificate Verification'))

@section('content')
@php
    // Controller should pass: $status ('valid'|'modified'|'missing'), $certificate, and $code.
    $status   = $status   ?? 'missing';
    $cert     = $certificate ?? null;
    $code     = $code     ?? data_get($cert,'verification_code') ?? data_get($cert,'code') ?? '';
    $checksum = data_get($cert,'checksum');
    $filePath = data_get($cert,'file_path');
    $holder   = data_get($cert,'user.name') ?? data_get($cert,'name');
    $hours    = data_get($cert,'hours');
    $minutes  = data_get($cert,'minutes');
    if (is_null($hours) && !is_null($minutes)) {
        $hours = round($minutes / 60, 2);
    }
    $issuedAt = data_get($cert,'issued_at') ? \Illuminate\Support\Carbon::parse(data_get($cert,'issued_at'))->format('Y-m-d') : null;
    $event    = data_get($cert,'opportunity.title') ?? data_get($cert,'event.title');
    $org      = data_get($cert,'organization.name') ?? data_get($cert,'opportunity.organization.name');
    $statusMap = ['valid'=>'success','modified'=>'warning','missing'=>'danger'];
    $badge = $statusMap[$status] ?? 'secondary';
@endphp

<div class="container py-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">{{ __('Certificate Verification') }}</h1>
        <p class="text-muted mb-0">{{ __('Verify authenticity of a SawaedUAE certificate by code.') }}</p>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge bg-{{ $badge }} px-3 py-2 text-uppercase">
                    @if($status === 'valid') {{ __('Valid') }}
                    @elseif($status === 'modified') {{ __('File Modified') }}
                    @else {{ __('Not Found') }}
                    @endif
                </span>
                @if($code)
                    <span class="text-muted">{{ __('Code') }}: <strong class="text-body">{{ $code }}</strong></span>
                @endif
            </div>

            @if($status === 'valid')
                <p class="mb-3">
                    {{ __('This certificate is valid and matches our records.') }}
                </p>
            @elseif($status === 'modified')
                <p class="mb-3 text-warning">
                    {{ __('Warning: The presented file does not match our stored checksum. The document may have been altered.') }}
                </p>
            @else
                <p class="mb-3 text-danger">
                    {{ __('We could not locate a certificate matching this code. Please check the code and try again.') }}
                </p>
            @endif

            <div class="row g-3">
                @if($holder)
                <div class="col-md-6">
                    <div class="p-3 rounded border bg-light">
                        <div class="text-muted small">{{ __('Certificate Holder') }}</div>
                        <div class="fw-semibold">{{ $holder }}</div>
                    </div>
                </div>
                @endif

                @if($event)
                <div class="col-md-6">
                    <div class="p-3 rounded border bg-light">
                        <div class="text-muted small">{{ __('Event / Opportunity') }}</div>
                        <div class="fw-semibold">{{ $event }}</div>
                    </div>
                </div>
                @endif

                @if($org)
                <div class="col-md-6">
                    <div class="p-3 rounded border bg-light">
                        <div class="text-muted small">{{ __('Organization') }}</div>
                        <div class="fw-semibold">{{ $org }}</div>
                    </div>
                </div>
                @endif

                @if(!is_null($hours))
                <div class="col-md-6">
                    <div class="p-3 rounded border bg-light">
                        <div class="text-muted small">{{ __('Volunteer Hours') }}</div>
                        <div class="fw-semibold">{{ $hours }}</div>
                    </div>
                </div>
                @endif

                @if($issuedAt)
                <div class="col-md-6">
                    <div class="p-3 rounded border bg-light">
                        <div class="text-muted small">{{ __('Issued Date') }}</div>
                        <div class="fw-semibold">{{ $issuedAt }}</div>
                    </div>
                </div>
                @endif

                @if($checksum)
                <div class="col-md-12">
                    <div class="p-3 rounded border">
                        <div class="text-muted small">{{ __('Checksum (SHA-256)') }}</div>
                        <code class="d-block text-wrap" style="word-break:break-all">{{ $checksum }}</code>
                        <div class="small text-muted mt-1">{{ __('Used to validate file integrity.') }}</div>
                    </div>
                </div>
                @endif
            </div>

            @if($filePath)
                <div class="mt-4 d-flex gap-2">
                    <a class="btn btn-outline-primary" href="{{ url($filePath) }}" target="_blank" rel="noopener">
                        {{ __('Download Certificate PDF') }}
                    </a>
                    <a class="btn btn-light" href="{{ url()->current() }}" onclick="window.location.reload(); return false;">
                        {{ __('Re-check') }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="text-muted small">
        {{ __('If you believe this result is incorrect, please contact the issuing organization or SawaedUAE support.') }}
    </div>
</div>
@endsection
