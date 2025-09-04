import React from "react";
export default function OrganizerPortal() {
  // TODO: Connect to backend for real data
  return (
    <div className="max-w-5xl mx-auto py-10 px-4">
      <h2 className="text-2xl font-bold mb-6">بوابة الجهات المنظمة</h2>
      <div className="mb-8 bg-yellow-50 p-5 rounded-xl border-l-4 border-yellow-600 text-yellow-900">
        <b>مرحباً بالجهة!</b> يمكنك إدارة فعالياتك، مراجعة طلبات المتطوعين، والتواصل مع المشاركين من هنا.
      </div>
      <div className="grid md:grid-cols-2 gap-8">
        <div className="bg-white rounded-xl shadow p-6">
          <div className="font-bold mb-2">إدارة الفعاليات</div>
          <ul className="list-disc ml-5 text-gray-700">
            <li>إنشاء فعالية جديدة</li>
            <li>تعديل الفعاليات الحالية</li>
            <li>إلغاء/تأجيل الفعالية</li>
          </ul>
        </div>
        <div className="bg-white rounded-xl shadow p-6">
          <div className="font-bold mb-2">طلبات المتطوعين</div>
          <ul className="list-disc ml-5 text-gray-700">
            <li>عرض الطلبات وقبول/رفض المتطوعين</li>
            <li>إرسال إشعارات للمشاركين</li>
            <li>تصدير التقارير</li>
          </ul>
        </div>
      </div>
    </div>
  );
}
