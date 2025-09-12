<?php
function files($root) {
  $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root,
    FilesystemIterator::SKIP_DOTS|FilesystemIterator::FOLLOW_SYMLINKS));
  foreach ($rii as $f) if ($f->isFile() && preg_match('/\.blade\.php$/',$f)) yield (string)$f;
}

$changedFiles = [];

foreach (files('resources/views') as $f) {
  $c0 = file_get_contents($f); $c = $c0;

  // 1) Literal/misspelled include -> real Blade include
  $c = preg_replace('/\(\s*[\'"]\s*partials\.au?h_menu\s*[\'"]\s*\)/', "@include('partials.auth_menu')", $c);

  // 2) Collapse repeated @include chains down to one
  // reduce any "@include   @include" pairs repeatedly
  while (preg_match('/@include\s*@include/s', $c)) {
    $c = preg_replace('/@include\s*@include/s', '@include ', $c);
  }
  // normalize whitespace around include call in case of mess
  $c = preg_replace('/@include\s*\(\s*([\'"])partials\.auth_menu\1\s*\)/', "@include('partials.auth_menu')", $c);

  // 3) Remove any "Admin Login" line (public header)
  $c = preg_replace('/^.*Admin Login.*\R?/m', '', $c);

  // 4) Convert GET /logout anchors to POST form (idempotent)
  $c = preg_replace('#<a[^>]*href=["\']/logout["\'][^>]*>(.*?)</a>#is',
    '<form method="POST" action="{{ route(\'logout.perform\') }}" style="display:inline;">'
    .'<input type="hidden" name="_token" value="{{ csrf_token() }}">'
    .'<button type="submit" class="nav-link btn btn-link p-0 m-0">$1</button></form>', $c);

  if ($c !== $c0) {
    @copy($f, $f.'.bak.'.date('Ymd_His'));
    file_put_contents($f, $c);
    $changedFiles[] = $f;
  }
}

// 5) Ensure the inline auth menu partial exists (simple, JS-free)
$partial = "resources/views/partials/auth_menu.blade.php";
if (!is_dir(dirname($partial))) mkdir(dirname($partial), 0775, true);
$partialBody = <<<'BLADE'
@auth
  <li class="nav-item"><a class="nav-link" href="{{ route('my.profile') }}">My Profile</a></li>
  <li class="nav-item">
    <form method="POST" action="{{ route('logout.perform') }}" class="d-inline">
      @csrf
      <button type="submit" class="nav-link btn btn-link p-0 m-0 align-baseline">Logout</button>
    </form>
  </li>
@else
  <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
@endauth
BLADE;
file_put_contents($partial, $partialBody);

// 6) Ensure login form has @csrf
$login = 'resources/views/auth/login.blade.php';
if (file_exists($login)) {
  $c0 = file_get_contents($login); $c = $c0;
  if (strpos($c, '@csrf') === false) {
    $c = preg_replace('/(<form\b[^>]*>)/i', "$1\n    @csrf", $c, 1);
    if ($c !== $c0) { @copy($login, $login.'.bak.'.date('Ymd_His')); file_put_contents($login, $c); }
  }
}

echo "changed:\n".implode("\n",$changedFiles)."\n";
