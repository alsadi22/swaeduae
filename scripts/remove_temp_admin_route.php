<?php
$path = __DIR__ . '/../routes/web.php';
$c = file_get_contents($path);
$c = preg_replace('#// ===== TEMP_ADMIN_ROUTE_START.*?TEMP_ADMIN_ROUTE_END =====\\n#s', '', $c);
file_put_contents($path, $c);
echo "Temp admin route removed.\n";
