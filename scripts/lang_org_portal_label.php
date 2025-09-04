<?php
function merge($file,$arr){$cur=file_exists($file)?include $file:[]; if(!is_array($cur))$cur=[]; file_put_contents($file,"<?php\n\nreturn ".var_export($cur+$arr,true).";\n"); echo "Merged: $file\n";}
$en=['org_portal'=>'Org Portal','applicants'=>'Applicants','approve'=>'Approve','reject'=>'Reject','back'=>'Back'];
$ar=['org_portal'=>'بوابة الجهات','applicants'=>'المتقدمون','approve'=>'قبول','reject'=>'رفض','back'=>'رجوع'];
merge(__DIR__.'/../resources/lang/en/swaed.php',$en);
merge(__DIR__.'/../resources/lang/ar/swaed.php',$ar);
