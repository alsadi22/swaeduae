@php $s = $appSettings['social'] ?? []; @endphp
@if(collect($s)->filter()->isNotEmpty())
<div class="d-flex gap-3">
  @if(!empty($s['facebook']))  <a href="{{ $s['facebook'] }}" target="_blank" aria-label="Facebook"><i class="fa-brands fa-facebook fa-lg"></i></a>@endif
  @if(!empty($s['instagram'])) <a href="{{ $s['instagram'] }}" target="_blank" aria-label="Instagram"><i class="fa-brands fa-instagram fa-lg"></i></a>@endif
  @if(!empty($s['twitter']))   <a href="{{ $s['twitter'] }}" target="_blank" aria-label="Twitter"><i class="fa-brands fa-x-twitter fa-lg"></i></a>@endif
  @if(!empty($s['linkedin']))  <a href="{{ $s['linkedin'] }}" target="_blank" aria-label="LinkedIn"><i class="fa-brands fa-linkedin fa-lg"></i></a>@endif
  @if(!empty($s['whatsapp']))  <a href="{{ $s['whatsapp'] }}" target="_blank" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp fa-lg"></i></a>@endif
</div>
@endif
