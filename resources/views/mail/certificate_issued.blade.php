<p>Dear Volunteer,</p>
<p>Your certificate has been issued.</p>
<ul>
  <li><strong>Code:</strong> {{ $c->code }}</li>
  <li><strong>Hours:</strong> {{ number_format($c->hours ?? 0,2) }}</li>
  <li><strong>Verify:</strong> <a href="{{ url('/qr/verify/'.$c->code) }}">{{ url('/qr/verify/'.$c->code) }}</a></li>
</ul>
<p>Thank you for your service.<br/>SwaedUAE</p>
