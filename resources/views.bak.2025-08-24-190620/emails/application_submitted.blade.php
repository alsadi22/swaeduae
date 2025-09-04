<!doctype html><html><body style="font-family:Arial,sans-serif">
  <h2 style="color:#0b3b5a;margin:0 0 8px">{{ __('Thanks for applying!') }}</h2>
  <p style="margin:0 0 12px">{{ __('Hi') }} {{ $user->name }},</p>
  <p style="margin:0 0 12px">
    {{ __('We received your application for') }} <strong>{{ $opportunity->title }}</strong>.
    {{ __('We attached a calendar invite (.ics) with the event time.') }}
  </p>
  <p style="margin:0 0 8px"><strong>{{ __('When') }}:</strong>
    {{ optional($opportunity->starts_at)->format('d M Y H:i') }} —
    {{ optional($opportunity->ends_at)->format('d M Y H:i') }}</p>
  <p style="margin:0 0 8px"><strong>{{ __('Where') }}:</strong>
    {{ $opportunity->location ?? $opportunity->city ?? 'UAE' }}</p>
  <p style="margin:16px 0 8px">
    <a href="{{ route('public.opportunities.show',$opportunity) }}" style="background:#0fb9b1;color:#fff;padding:8px 12px;border-radius:6px;text-decoration:none">
      {{ __('View opportunity') }}
    </a>
  </p>
  <p style="color:#666;font-size:12px;margin-top:20px">{{ __('This is an automated message from SawaedUAE.') }}</p>
</body></html>
