import React from "react";
import Header from "../components/Header";
import Footer from "../components/Footer";
export default function Analytics() {
  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <div className="max-w-4xl mx-auto mt-8 bg-white rounded-xl shadow-lg p-8">
        <h2 className="text-2xl font-bold mb-6">تحليلات الموقع</h2>
        <div className="grid grid-cols-2 gap-6 mb-8">
          <div className="bg-green-100 p-6 rounded-xl text-center">
            <div className="text-2xl font-bold text-green-700">43,812</div>
            <div>عدد الزيارات</div>
          </div>
          <div className="bg-blue-100 p-6 rounded-xl text-center">
            <div className="text-2xl font-bold text-blue-700">2,134</div>
            <div>عدد المسجلين الجدد</div>
          </div>
          <div className="bg-orange-100 p-6 rounded-xl text-center">
            <div className="text-2xl font-bold text-orange-700">186</div>
            <div>عدد الفعاليات</div>
          </div>
          <div className="bg-purple-100 p-6 rounded-xl text-center">
            <div className="text-2xl font-bold text-purple-700">743</div>
            <div>عدد الرسائل المرسلة</div>
          </div>
        </div>
      </div>
      <Footer />
    </div>
  );
}
