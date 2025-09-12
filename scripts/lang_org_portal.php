<?php
function merge($file,$arr){$cur=file_exists($file)?include $file:[]; if(!is_array($cur))$cur=[]; file_put_contents($file,"<?php\n\nreturn ".var_export($cur+$arr,true).";\n"); echo "Merged: $file\n";}
$en=[
  'not_org_user'=>'Not authorized for organization portal.',
  'org_created'=>'Organization saved.',
  'org_setup'=>'Organization Setup',
  'org_dashboard'=>'Organization Dashboard',
  'post_opportunity'=>'Post Opportunity',
  'manage_opportunities'=>'Manage Opportunities',
  'total_opportunities'=>'Total Opportunities',
  'published'=>'Published',
  'pending_apps'=>'Pending Applications',
  'title'=>'Title','description'=>'Description','save'=>'Save','cancel'=>'Cancel',
  'license_no'=>'License No.','start_time'=>'Start','end_time'=>'End','draft'=>'Draft','closed'=>'Closed','target'=>'Target'
];
$ar=[
  'not_org_user'=>'ليست لديك صلاحية دخول بوابة الجهات.',
  'org_created'=>'تم حفظ جهة التطوع.',
  'org_setup'=>'إعداد الجهة',
  'org_dashboard'=>'لوحة الجهة',
  'post_opportunity'=>'إنشاء فرصة',
  'manage_opportunities'=>'إدارة الفرص',
  'total_opportunities'=>'إجمالي الفرص',
  'published'=>'منشور',
  'pending_apps'=>'طلبات قيد الانتظار',
  'title'=>'العنوان','description'=>'الوصف','save'=>'حفظ','cancel'=>'إلغاء',
  'license_no'=>'رقم الترخيص','start_time'=>'البداية','end_time'=>'النهاية','draft'=>'مسودة','closed'=>'مغلق','target'=>'العدد المطلوب'
];
merge(__DIR__.'/../resources/lang/en/swaed.php',$en);
merge(__DIR__.'/../resources/lang/ar/swaed.php',$ar);
