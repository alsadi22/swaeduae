@extends('layouts.admin')
@section('title','QR Verify')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-3">QR Verify</h1>
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="code" value="{{ $code }}" class="form-control" placeholder="Code">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    @if($status)
    <div class="card">
        <div class="card-body">
            @if($status === 'valid')
                <div class="alert alert-success mb-3">Valid certificate</div>
            @elseif($status === 'expired')
                <div class="alert alert-warning mb-3">Certificate revoked</div>
            @else
                <div class="alert alert-danger mb-3">Code not found</div>
            @endif
            @if($certificate)
                <p class="mb-1"><strong>{{ $certificate->code }}</strong></p>
                <p class="mb-3">{{ $certificate->hours }} {{ __('hours') }}</p>
                <a href="{{ route('qr.verify', $certificate->code) }}" class="btn btn-outline-secondary btn-sm" target="_blank">Public verify</a>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection

