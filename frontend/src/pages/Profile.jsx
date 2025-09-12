import React from "react";
import Header from "../components/Header";
import Footer from "../components/Footer";
export default function Profile() {
  return (
    <div className="min-h-screen bg-gray-100">
      <Header />
      <div className="max-w-3xl mx-auto mt-10 bg-white p-8 rounded-2xl shadow-xl">
        <h2 className="text-2xl font-bold mb-2 text-blue-900">ملفي الشخصي</h2>
        <div className="flex flex-col gap-4">
          <div><strong>الاسم:</strong> أحمد محمد</div>
          <div><strong>البريد الإلكتروني:</strong> ahmed@email.com</div>
          <div><strong>عدد ساعات التطوع:</strong> 56 ساعة</div>
          <div><strong>الفرص المسجلة:</strong> 3</div>
          <div><strong>الشهادات:</strong> <a href="#" className="text-blue-700 underline">عرض الشهادات</a></div>
        </div>
      </div>
      <Footer />
    </div>
  );
}
