import React, { useEffect, useState } from "react";
export default function OrgAdminDashboard() {
  const [stats, setStats] = useState(null);
  useEffect(()=>{ fetch("/api/orgadmin/stats").then(r=>r.json()).then(setStats); },[]);
  if (!stats) return <div className="mt-20 text-center">جاري التحميل...</div>;
  return (
    <div className="max-w-4xl mx-auto mt-10">
      <h1 className="text-2xl font-bold mb-6">لوحة تحكم جهة التنظيم</h1>
      <div className="bg-white rounded-2xl shadow p-6 grid grid-cols-2 gap-6">
        <div>
          <div className="text-gray-500">عدد الفعاليات</div>
          <div className="font-bold text-2xl">{stats.events_count}</div>
        </div>
        <div>
          <div className="text-gray-500">عدد المتطوعين المسجلين</div>
          <div className="font-bold text-2xl">{stats.volunteers_count}</div>
        </div>
      </div>
      <div className="mt-6">
        <a href="/admin/events" className="bg-blue-600 px-4 py-2 text-white rounded-full mr-3">إدارة الفعاليات</a>
        <a href="/organizations" className="bg-gray-200 px-4 py-2 rounded-full">كل الجهات</a>
      </div>
    </div>
  );
}
