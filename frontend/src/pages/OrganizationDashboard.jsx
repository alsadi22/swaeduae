import React from "react";
export default function OrganizationDashboard() {
  return (
    <div className="max-w-5xl mx-auto py-10">
      <h2 className="text-2xl font-bold mb-6">لوحة تحكم المؤسسة</h2>
      <div className="bg-white rounded-lg shadow p-8 mb-6">
        <div className="font-bold mb-2">عدد فعاليات المؤسسة: 4</div>
        <div className="font-bold mb-2">عدد المتطوعين المسجلين: 120</div>
      </div>
      <div className="grid md:grid-cols-2 gap-8">
        <div className="bg-white rounded-xl shadow p-6 border">
          <h3 className="font-bold mb-2">إدارة فعاليات المؤسسة</h3>
          <button className="py-2 px-4 bg-green-600 text-white rounded">إضافة فعالية</button>
        </div>
        <div className="bg-white rounded-xl shadow p-6 border">
          <h3 className="font-bold mb-2">إدارة طلبات المتطوعين</h3>
          <button className="py-2 px-4 bg-green-600 text-white rounded">عرض الطلبات</button>
        </div>
      </div>
    </div>
  );
}
