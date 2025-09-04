<p>{{ __('Dear') }} {{ $user->name ?? '' }},</p>
<p>{{ __('Congratulations! Your volunteer certificate is attached.') }}</p>
@if(!empty($c->code))
<p>{{ __('Verification code') }}: <strong>{{ $c->code }}</strong></p>
<p>{{ __('Verify here') }}: <a href="{{ url('/verify/'.$c->code) }}">{{ url('/verify/'.$c->code) }}</a></p>
@endif
<p>{{ __('Thank you for volunteering with SawaedUAE.') }}</p>
