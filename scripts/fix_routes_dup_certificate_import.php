<?php
$path = __DIR__ . '/../routes/web.php';
$c = file_get_contents($path);
if ($c === false) { fwrite(STDERR, "Cannot read routes/web.php\n"); exit(1); }

# Remove ALL use-lines for CertificateController (with or without alias)
$c = preg_replace('/^\\s*use\\s+App\\\\Http\\\\Controllers\\\\CertificateController(?:\\s+as\\s+\\w+)?;\\s*$/m', '', $c);

# Normalize any remaining short class refs to FQCN
$c = str_replace('CertificateController::class', '\\App\\Http\\Controllers\\CertificateController::class', $c);

file_put_contents($path, $c);
echo "Cleaned duplicate CertificateController imports and normalized references.\n";
