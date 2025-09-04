import React from "react";
import Header from "../components/Header";
import Footer from "../components/Footer";
export default function AdminDashboard() {
  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <div className="max-w-5xl mx-auto mt-10 bg-white p-8 rounded-2xl shadow-xl">
        <h2 className="text-3xl font-bold mb-6 text-blue-900">لوحة الإدارة</h2>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div className="bg-blue-100 rounded-xl p-6 text-center">
            <div className="text-3xl font-bold text-blue-700">2150</div>
            <div>عدد المتطوعين</div>
          </div>
          <div className="bg-green-100 rounded-xl p-6 text-center">
            <div className="text-3xl font-bold text-green-700">38</div>
            <div>عدد الجهات</div>
          </div>
          <div className="bg-orange-100 rounded-xl p-6 text-center">
            <div className="text-3xl font-bold text-orange-700">73</div>
            <div>عدد الفرص الفعالة</div>
          </div>
        </div>
        <div className="mt-10 grid grid-cols-1 md:grid-cols-2 gap-8">
          <div className="bg-white border rounded-xl p-6">
            <h3 className="text-xl font-bold mb-3 text-blue-900">إدارة المتطوعين</h3>
            <ul>
              <li>أحمد محمد - ahmed@email.com - <button className="text-red-600">حذف</button></li>
              <li>سارة علي - sara@email.com - <button className="text-red-600">حذف</button></li>
            </ul>
          </div>
          <div className="bg-white border rounded-xl p-6">
            <h3 className="text-xl font-bold mb-3 text-blue-900">إدارة الفرص التطوعية</h3>
            <button className="bg-green-600 text-white rounded px-4 py-2 mb-2">إضافة فرصة</button>
            <ul>
              <li>زيارة منزل المسنين <button className="text-red-600">حذف</button></li>
              <li>الرسم مع سلامة <button className="text-red-600">حذف</button></li>
            </ul>
          </div>
        </div>
        <div className="mt-10">
          <h3 className="text-xl font-bold mb-3 text-blue-900">إعدادات الموقع</h3>
          <button className="bg-yellow-500 text-white rounded px-4 py-2">حفظ الإعدادات</button>
        </div>
      </div>
      <Footer />
    </div>
  );
}
