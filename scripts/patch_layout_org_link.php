<?php
$file = __DIR__ . '/../resources/views/layouts/app.blade.php';
if (!file_exists($file)) { fwrite(STDERR,"Layout not found: $file\n"); exit(1); }
$src = file_get_contents($file);

// Already present?
if (strpos($src, "route('org.dashboard')") !== false || strpos($src, 'org_portal') !== false) {
  echo "Org link already present.\n"; exit(0);
}

$btn = <<<'BLADE'

{{-- Org Portal (admins & org users) --}}
@auth
  @php $u = auth()->user(); @endphp
  @if( ($u->is_admin ?? 0) == 1 || (($u->role ?? '') === 'org') )
    <a href="{{ route('org.dashboard') }}" class="btn btn-outline-primary btn-sm ms-2">
      {{ __('swaed.org_portal') ?? 'Org Portal' }}
    </a>
  @endif
@endauth
BLADE;

// Try to place it just before </nav> (most common)
$placed = false;
if (strpos($src, '</nav>') !== false) {
  $src = str_replace('</nav>', $btn . "\n</nav>", $src);
  $placed = true;
}

// Fallback: place before </header> if nav not found
if (!$placed && strpos($src, '</header>') !== false) {
  $src = str_replace('</header>', $btn . "\n</header>", $src);
  $placed = true;
}

// Last fallback: append at end
if (!$placed) {
  $src .= "\n".$btn."\n";
}

file_put_contents($file, $src);
echo "Org Portal link injected.\n";
