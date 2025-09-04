use strict; use warnings; local $/ = undef; my $c=<>;

# 1) Convert literal/misspelled include calls into a real Blade include
$c =~ s/\(\s*["']partials\.auth_menu["']\s*\)/@include('partials.auth_menu')/g;
$c =~ s/\(\s*["']partials\.auh_menu["']\s*\)/@include('partials.auth_menu')/g;

# 2) Collapse repeated @include chains to a single include
$c =~ s/\@include(?:\s*\@include)+\s*\(\s*(['"])partials\.auth_menu\1\s*\)/@include($1partials.auth_menu$1)/g;
$c =~ s/\@include\s*\@include\s*/@include /g; # stray double

print $c;
