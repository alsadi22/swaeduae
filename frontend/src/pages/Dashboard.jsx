import React from "react";
import { Link } from "react-router-dom";
export default function Dashboard() {
  return (
    <div className="max-w-6xl mx-auto py-10">
      <h2 className="text-2xl font-bold mb-8">لوحة تحكم المتطوع</h2>
      <div className="grid md:grid-cols-3 gap-6">
        <Link to="/profile" className="bg-white rounded-xl shadow p-6 hover:shadow-lg transition border">
          <h3 className="text-lg font-bold mb-2">ملفي الشخصي</h3>
          <p>عرض وتحديث بياناتك ومعلومات التحقق</p>
        </Link>
        <Link to="/my-events" className="bg-white rounded-xl shadow p-6 hover:shadow-lg transition border">
          <h3 className="text-lg font-bold mb-2">فعالياتي</h3>
          <p>إدارة الفعاليات التي شاركت بها والتسجيل للفعاليات القادمة</p>
        </Link>
        <Link to="/certificates" className="bg-white rounded-xl shadow p-6 hover:shadow-lg transition border">
          <h3 className="text-lg font-bold mb-2">الشهادات</h3>
          <p>عرض وتحميل شهادات التطوع الخاصة بك</p>
        </Link>
      </div>
    </div>
  );
}
