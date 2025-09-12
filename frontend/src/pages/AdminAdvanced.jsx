import React, { useState } from "react";
import Header from "../components/Header";
import Footer from "../components/Footer";
export default function AdminAdvanced() {
  const [logs] = useState([
    "تم حذف المستخدم أحمد",
    "تمت إضافة فرصة جديدة: يوم التطوع الوطني",
    "تمت الموافقة على صورة جديدة",
  ]);
  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <div className="max-w-4xl mx-auto mt-8 bg-white rounded-xl shadow-lg p-8">
        <h2 className="text-2xl font-bold mb-6">لوحة تحكم المشرف المتقدم</h2>
        <div className="mb-8">
          <h3 className="text-xl font-bold mb-2">إعدادات الموقع الشاملة</h3>
          <button className="bg-yellow-600 text-white rounded px-4 py-2">تحديث شعار الموقع</button>
        </div>
        <div className="mb-8">
          <h3 className="text-xl font-bold mb-2">تحميل صورة/ملف</h3>
          <input type="file" className="border rounded p-2" />
        </div>
        <div className="mb-8">
          <h3 className="text-xl font-bold mb-2">سجل العمليات والإجراءات</h3>
          <ul className="list-disc ml-6">
            {logs.map((log, i) => <li key={i}>{log}</li>)}
          </ul>
        </div>
        <div className="mb-8">
          <h3 className="text-xl font-bold mb-2">إرسال رسالة لجميع المستخدمين</h3>
          <textarea className="border rounded w-full mb-2 p-2" rows={2} placeholder="اكتب رسالتك..." />
          <button className="bg-blue-600 text-white rounded px-4 py-2">إرسال</button>
        </div>
      </div>
      <Footer />
    </div>
  );
}
