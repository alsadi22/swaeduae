@php
  $isAr = app()->getLocale() === 'ar';
@endphp
<!doctype html>
<html lang="{{ $isAr ? 'ar' : 'en' }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <title>{{ $orgName }} — {{ __('Application update') }}</title>
</head>
<body style="margin:0;padding:0;background:#f6f7fb;font-family:Tahoma,Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
      <td align="center" style="padding:24px;">
        <table width="640" cellpadding="0" cellspacing="0" role="presentation" style="background:#fff;border-radius:12px;overflow:hidden">
          <tr>
            <td style="background:{{ $brandColor }};padding:16px;">
              <table width="100%" role="presentation"><tr>
                <td style="color:#fff;font-weight:700;font-size:18px;">
                  {{ $orgName }}
                </td>
                <td align="{{ $isAr ? 'left' : 'right' }}">
                  @if($logo)
                    <img src="{{ $logo }}" alt="{{ $orgName }}" style="height:36px;display:block">
                  @endif
                </td>
              </tr></table>
            </td>
          </tr>
          <tr>
            <td style="padding:24px;color:#111;">
              <h1 style="margin:0 0 8px;font-size:20px;">
                {{ __('Application update') }} — {{ $opportunityTitle }}
              </h1>

              @if($decision === 'approved')
                <p style="margin:0 0 8px;">{{ $isAr ? 'تمت الموافقة على طلبكم. نرحب بانضمامكم! ستصلكم التفاصيل عبر البريد قريباً.' : 'Your application has been approved. Welcome aboard! Details will follow shortly.' }}</p>
              @elseif($decision === 'waitlist')
                <p style="margin:0 0 8px;">{{ $isAr ? 'تم إدراجكم في قائمة الانتظار. سنقوم بإعلامكم عند توفر الشواغر.' : 'You have been placed on the waitlist. We will notify you as spots open.' }}</p>
              @else
                <p style="margin:0 0 8px;">{{ $isAr ? 'نأسف لعدم قبول طلبكم في هذه الفترة. نرحب بتقديمكم لفرص أخرى.' : 'We’re sorry we can’t proceed this time. We encourage you to apply to other opportunities.' }}</p>
              @endif

              @if($note)
                <div style="margin:12px 0;padding:12px;border:1px solid #eee;border-radius:8px;background:#fafafa;">
                  <strong>{{ $isAr ? 'ملاحظة:' : 'Note:' }}</strong>
                  <div>{{ $note }}</div>
                </div>
              @endif

              <p style="margin:16px 0 0;color:#555;">{{ $isAr ? 'مع خالص الشكر،' : 'With thanks,' }}<br>{{ $orgName }}</p>
            </td>
          </tr>
          <tr>
            <td style="padding:16px;background:#f1f3f6;color:#666;text-align:center;font-size:12px;">
              © {{ date('Y') }} {{ $orgName }}
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
