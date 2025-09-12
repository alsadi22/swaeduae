import React, { useState } from "react";
export default function Notifications() {
  // TODO: Load notifications from backend
  const [open, setOpen] = useState(false);
  const notifications = [
    { id: 1, text: "تم قبولك في فعالية تنظيف الشاطئ", date: "2025-08-01" },
    { id: 2, text: "شهادة جديدة متاحة للتحميل", date: "2025-08-02" },
  ];
  return (
    <div className="relative">
      <button onClick={() => setOpen(!open)} className="relative">
        <span className="material-icons">notifications</span>
        {notifications.length > 0 && <span className="absolute top-0 right-0 inline-block w-2 h-2 bg-red-600 rounded-full"></span>}
      </button>
      {open && (
        <div className="absolute right-0 mt-2 w-72 bg-white border rounded shadow-lg z-10">
          <ul>
            {notifications.map(n => (
              <li key={n.id} className="p-3 border-b last:border-b-0">{n.text}<div className="text-xs text-gray-400">{n.date}</div></li>
            ))}
            {notifications.length === 0 && <li className="p-3 text-center text-gray-400">لا يوجد إشعارات</li>}
          </ul>
        </div>
      )}
    </div>
  );
}
