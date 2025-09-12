use strict; use warnings; local $/ = undef; my $c=<>;

# If login form lacks @csrf, insert it after the opening <form>
if ($c !~ /\@csrf/) {
  $c =~ s/(<form\b[^>]*>)/$1\n    \@csrf/s;
}
print $c;
