<?php
$f = $argv[1] ?? null;
if (!$f || !is_file($f)) {
  fwrite(STDERR, "Usage: php tools/add_forgot_link.php path/to/file.blade.php\n");
  exit(1);
}
$s = file_get_contents($f);
if (strpos($s, "password.request") !== false) {
  echo "Already has link: $f\n"; exit;
}
$snip = <<<'BLADE'
    @if (Route::has('password.request'))
      <div class="mt-2">
        <a href="{{ route('password.request') }}" class="small text-muted">{{ __('Forgot your password?') }}</a>
      </div>
    @endif

BLADE;
$s = preg_replace('/<\/form>/i', $snip."</form>", $s, 1);
file_put_contents($f.".tmp", $s);
rename($f.".tmp", $f);
echo "Patched: $f\n";
