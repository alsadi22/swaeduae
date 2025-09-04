@if(!empty($latestCert))
  @php
    $issued = $latestCert->issued_at ?? $latestCert->issued_date ?? null;
    try { $issuedFmt = $issued ? \Carbon\Carbon::parse($issued)->format('d M Y') : null; }
    catch (\Throwable $e) { $issuedFmt = $issued; }
  @endphp

  <div class="mt-2 flex flex-col gap-2">
    <div class="text-sm text-gray-600">
      @if($issuedFmt)
        <span><strong>{{ __('Issued') }}:</strong> {{ $issuedFmt }}</span>
      @endif
      @if(!empty($latestCert->opportunity_title))
        <span class="mx-2">·</span>
        <span><strong>{{ __('Opportunity') }}:</strong> {{ $latestCert->opportunity_title }}</span>
      @endif
      <span class="mx-2">·</span>
      <span><strong>{{ __('Code') }}:</strong> {{ $latestCert->code }}</span>
    </div>

    <div class="flex gap-2">
      <a class="btn btn-sm btn-primary" href="{{ $latestCert->file_path }}" target="_blank" rel="noopener">
        {{ __('Download Latest Certificate') }}
      </a>
      <a class="btn btn-sm btn-outline-secondary" href="{{ url('verify/' . ($latestCert->code ?? '')) }}" target="_blank" rel="noopener">
        {{ __('Verify') }}
      </a>
      <a class="btn btn-sm btn-outline-primary" href="{{ url('/my-certificates') }}">
        {{ __('My Certificates') }}
      </a>
    </div>
  </div>
@endif
