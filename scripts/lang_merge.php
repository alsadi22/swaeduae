<?php
function merge_lang($file, $new) {
    $arr = [];
    if (file_exists($file)) {
        $arr = include $file;
        if (!is_array($arr)) $arr = [];
    }
    $merged = $arr + $new; // keep existing keys, add missing
    $code = "<?php\n\nreturn " . var_export($merged, true) . ";\n";
    file_put_contents($file, $code);
    echo "Merged: $file\n";
}

$en = [
  'search' => 'Search',
  'search_placeholder' => 'Search events...',
  'filter' => 'Filter',
  'reset_filters' => 'Reset',
  'view_details' => 'View details',
  'no_events_found' => 'No events found.',
  'pending_applications' => 'Pending Applications',
  'applicant' => 'Applicant',
  'email' => 'Email',
  'actions' => 'Actions',
  'approve' => 'Approve',
  'reject' => 'Reject',
  'no_pending_applications' => 'No pending applications.',
  'location_placeholder' => 'City or region',
];

$ar = [
  'search' => 'بحث',
  'search_placeholder' => 'ابحث عن فعاليات...',
  'filter' => 'تصفية',
  'reset_filters' => 'إعادة تعيين',
  'view_details' => 'عرض التفاصيل',
  'no_events_found' => 'لا توجد فعاليات.',
  'pending_applications' => 'الطلبات قيد الانتظار',
  'applicant' => 'المتقدم',
  'email' => 'البريد الإلكتروني',
  'actions' => 'إجراءات',
  'approve' => 'موافقة',
  'reject' => 'رفض',
  'no_pending_applications' => 'لا توجد طلبات قيد الانتظار.',
  'location_placeholder' => 'المدينة أو المنطقة',
];

merge_lang(__DIR__ . '/../resources/lang/en/swaed.php', $en);
merge_lang(__DIR__ . '/../resources/lang/ar/swaed.php', $ar);
