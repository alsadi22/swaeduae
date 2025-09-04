<?php
function merge_lang($file, $new){
  $arr = file_exists($file) ? include $file : [];
  if (!is_array($arr)) $arr = [];
  $code = "<?php\n\nreturn " . var_export($arr + $new, true) . ";\n";
  file_put_contents($file, $code);
  echo "Merged: $file\n";
}
$en = ['test'=>'Test','ok_configured'=>'Looks good!','missing_keys'=>'Missing or invalid keys.'];
$ar = ['test'=>'اختبار','ok_configured'=>'الإعدادات تبدو صحيحة','missing_keys'=>'مفاتيح ناقصة أو غير صحيحة.'];
merge_lang(__DIR__.'/../resources/lang/en/swaed.php',$en);
merge_lang(__DIR__.'/../resources/lang/ar/swaed.php',$ar);
