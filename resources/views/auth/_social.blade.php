@php
use App\Support\Integrations as I;
$defs = collect([
  ['key'=>'uaepass','label'=>'Continue with UAE PASS','emoji'=>'🟢'],
  ['key'=>'google','label'=>'Continue with Google','emoji'=>'🟠'],
  ['key'=>'apple','label'=>'Continue with Apple','emoji'=>'⚫'],
  ['key'=>'facebook','label'=>'Continue with Facebook','emoji'=>'🔵'],
]);
$enabled = $defs->filter(fn($p)=> I::enabled($p['key']) && I::id($p['key']) && I::secret($p['key']));
@endphp
@if($enabled->isNotEmpty())
  <div class="my-3">
    @foreach($enabled as $p)
      <a class="btn btn-outline-secondary w-100 mb-2" href="{{ route('oauth.redirect', $p['key']) }}">
        {{ $p['emoji'] }} {{ $p['label'] }}
      </a>
    @endforeach
  </div>
@endif
