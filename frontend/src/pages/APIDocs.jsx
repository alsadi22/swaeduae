import React from "react";
export default function APIDocs() {
  return (
    <div className="max-w-4xl mx-auto py-10 px-4">
      <h2 className="text-2xl font-bold mb-6">توثيق واجهة برمجة التطبيقات (API)</h2>
      <div className="mb-4 text-lg">
        يمكنك استخدام واجهات API للربط مع تطبيقات الجوال أو المواقع الأخرى.
      </div>
      <div className="bg-white shadow rounded-xl p-6 mb-4">
        <div className="font-bold mb-2">GET /api/events</div>
        <div className="text-sm text-gray-600 mb-2">إرجاع قائمة الفعاليات العامة</div>
        <pre className="bg-gray-100 rounded p-3 overflow-x-auto">{
`[
  { "id": 1, "title": "فعالية", "date": "2025-08-12", "city": "دبي" },
  ...
]`
}</pre>
      </div>
      <div className="bg-white shadow rounded-xl p-6 mb-4">
        <div className="font-bold mb-2">POST /api/register</div>
        <div className="text-sm text-gray-600 mb-2">تسجيل مستخدم جديد</div>
        <pre className="bg-gray-100 rounded p-3 overflow-x-auto">{
`{ "name": "اسم", "email": "mail@example.com", "password": "****" }`
}</pre>
      </div>
      {/* More API docs here */}
    </div>
  );
}
