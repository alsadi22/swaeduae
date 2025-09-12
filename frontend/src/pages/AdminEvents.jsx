import React, { useEffect, useState } from "react";
import axios from "axios";
export default function AdminEvents() {
  const [events, setEvents] = useState([]);
  useEffect(() => { axios.get("/api/admin/events").then(r => setEvents(r.data)); }, []);
  return (
    <div className="p-6">
      <h2 className="font-bold text-2xl mb-4">إدارة الفعاليات</h2>
      <table className="w-full border mb-6">
        <thead><tr><th>الفعالية</th><th>الموقع</th><th>التاريخ</th><th>إجراءات</th></tr></thead>
        <tbody>
          {events.map(e => (
            <tr key={e.id}><td>{e.title}</td><td>{e.location}</td><td>{e.date}</td><td><button className="bg-red-500 text-white px-2 rounded">حذف</button></td></tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
