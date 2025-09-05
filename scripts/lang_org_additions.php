<?php
function merge($file,$arr){$cur=file_exists($file)?include $file:[]; if(!is_array($cur))$cur=[]; file_put_contents($file,"<?php\n\nreturn ".var_export($cur+$arr,true).";\n"); echo "Merged: $file\n";}
$en=[
  'manage_opportunities'=>'Manage Opportunities','post_opportunity'=>'Post Opportunity','title'=>'Title','date'=>'Date',
  'location'=>'Location','status'=>'Status','target'=>'Target','type'=>'Type','start_time'=>'Start Time','end_time'=>'End Time',
  'description'=>'Description','actions'=>'Actions','save'=>'Save','updated'=>'Updated','saved'=>'Saved','edit'=>'Edit',
  'update'=>'Update','close'=>'Close','confirm_close'=>'Close this opportunity?','applicants'=>'Applicants','back'=>'Back',
  'settings'=>'Settings','organization'=>'Organization','license_no'=>'License No.','approve'=>'Approve','reject'=>'Reject',
  'applied_at'=>'Applied At'
];
$ar=[
  'manage_opportunities'=>'إدارة الفرص','post_opportunity'=>'إضافة فرصة','title'=>'العنوان','date'=>'التاريخ',
  'location'=>'الموقع','status'=>'الحالة','target'=>'العدد المستهدف','type'=>'النوع','start_time'=>'وقت البداية','end_time'=>'وقت النهاية',
  'description'=>'الوصف','actions'=>'الإجراءات','save'=>'حفظ','updated'=>'تم التحديث','saved'=>'تم الحفظ','edit'=>'تعديل',
  'update'=>'تحديث','close'=>'إغلاق','confirm_close'=>'هل تريد إغلاق الفرصة؟','applicants'=>'المتقدمون','back'=>'رجوع',
  'settings'=>'الإعدادات','organization'=>'الجهة','license_no'=>'رقم الترخيص','approve'=>'قبول','reject'=>'رفض',
  'applied_at'=>'تاريخ التقديم'
];
merge(__DIR__.'/../resources/lang/en/swaed.php',$en);
merge(__DIR__.'/../resources/lang/ar/swaed.php',$ar);
