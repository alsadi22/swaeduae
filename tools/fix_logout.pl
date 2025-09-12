use strict; use warnings; local $/ = undef; my $c=<>;

# Convert any GET /logout anchor to a POST form with CSRF
$c =~ s#<a[^>]*href=["']/logout["'][^>]*>(.*?)</a>#<form method="POST" action="{{ route('logout.perform') }}" style="display:inline;"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button type="submit" class="nav-link btn btn-link p-0 m-0">$1</button></form>#igs;

print $c;
