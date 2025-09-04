<!doctype html>
<html lang="en" dir="ltr">
  <body style="font-family: Arial, sans-serif; background:#fbf3d5; padding:20px;">
    <div style="max-width:640px;margin:auto;background:#ffffff;border-radius:8px;padding:24px;">
      <h2 style="margin-top:0;">{{ __('Congratulations! Your certificate is ready.') }}</h2>
      <p>
        {{ __('Name') }}: <strong>{{ $cert->user->name }}</strong><br>
        {{ __('Opportunity') }}: <strong>{{ optional($cert->opportunity)->title }}</strong><br>
        {{ __('Hours') }}: <strong>{{ number_format((float)($cert->hours ?? 0),2) }}</strong><br>
        {{ __('Issued Date') }}: <strong>{{ $cert->issued_date }}</strong><br>
        {{ __('Code') }}: <strong>{{ $cert->code ?? $cert->verification_code }}</strong>
      </p>
      @if($cert->file_path)
      <p>
        <a href="{{ url($cert->file_path) }}" style="display:inline-block;padding:10px 16px;border-radius:6px;background:#9cafaa;color:#fff;text-decoration:none;">
          {{ __('Download Certificate (PDF)') }}
        </a>
      </p>
      @endif
      <p>
        {{ __('Verify at') }}: <a href="{{ url('/verify/'.urlencode($cert->code ?? $cert->verification_code)) }}">{{ url('/verify/'.urlencode($cert->code ?? $cert->verification_code)) }}</a>
      </p>
      <hr>
      <p dir="rtl" style="text-align:right">
        <strong>تهانينا! شهادتك جاهزة.</strong><br>
        الاسم: <strong>{{ $cert->user->name }}</strong><br>
        الفرصة: <strong>{{ optional($cert->opportunity)->title }}</strong><br>
        الساعات: <strong>{{ number_format((float)($cert->hours ?? 0),2) }}</strong><br>
        تاريخ الإصدار: <strong>{{ $cert->issued_date }}</strong><br>
        الرمز: <strong>{{ $cert->code ?? $cert->verification_code }}</strong><br>
        @if($cert->file_path)
          <a href="{{ url($cert->file_path) }}">{{ __('تحميل الشهادة (PDF)') }}</a>
        @endif
      </p>
    </div>
  </body>
</html>
