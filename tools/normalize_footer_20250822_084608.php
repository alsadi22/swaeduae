<?php
$f = 'resources/views/layouts/app.blade.php';
$c = file_get_contents($f);

// Remove Argon footer include
$c = preg_replace('/^\h*@include(?:If)?\((["\'])argon_front[\/]_footer\1\)\h*\R/m', '', $c);

// Remove legacy footer-nav include
$c = preg_replace('/^\h*@include(?:If)?\((["\'])partials\.footer-nav\1\)\h*\R/m', '', $c);

// Remove any existing components.footer (avoid dupes)
$c = preg_replace('/^\h*@include(?:If)?\((["\'])components\.footer\1\)\h*\R/m', '', $c);

// Insert a single components.footer before </body>
$c = preg_replace('/<\/body>/', "  @include('components.footer')\n</body>", $c, 1);

file_put_contents($f, $c);
echo "OK: footer normalized\n";
