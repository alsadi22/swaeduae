<?php
function merge($file,$arr){$cur=file_exists($file)?include $file:[]; if(!is_array($cur))$cur=[]; file_put_contents($file,"<?php\n\nreturn ".var_export($cur+$arr,true).";\n");}
merge(__DIR__.'/../resources/lang/en/swaed.php', ['no_results'=>'No records']);
merge(__DIR__.'/../resources/lang/ar/swaed.php', ['no_results'=>'لا توجد سجلات']);
echo "lang updated\n";
