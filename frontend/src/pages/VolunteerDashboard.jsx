import React from "react";
import Header from "../components/Header";
import Footer from "../components/Footer";
export default function VolunteerDashboard() {
  return (
    <div dir="rtl">
      <Header />
      <main className="max-w-5xl mx-auto py-10 px-4">
        <h1 className="text-2xl font-bold mb-6 text-blue-900">لوحة المتطوع</h1>
        <div className="grid md:grid-cols-2 gap-8">
          <div className="bg-white rounded-xl shadow p-6">
            <h2 className="font-bold text-xl mb-3 text-orange-600">ساعات التطوع</h2>
            <p className="text-3xl font-extrabold text-blue-900">104</p>
            <span className="text-gray-500">ساعة منذ التسجيل</span>
          </div>
          <div className="bg-white rounded-xl shadow p-6">
            <h2 className="font-bold text-xl mb-3 text-orange-600">الشهادات المكتسبة</h2>
            <ul className="text-blue-900 font-medium">
              <li>شهادة تطوع في خدمة المجتمع (2025)</li>
              <li>شهادة إنجاز في مهرجان الرياضة (2024)</li>
            </ul>
          </div>
        </div>
      </main>
      <Footer />
    </div>
  );
}
