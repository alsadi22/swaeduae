import React, { useEffect, useState } from "react";
export default function EventCalendar() {
  const [events, setEvents] = useState([]);
  useEffect(()=>{ fetch("/api/events").then(r=>r.json()).then(setEvents); },[]);
  return (
    <div className="max-w-4xl mx-auto mt-10 bg-white rounded-2xl p-8 shadow">
      <h1 className="text-2xl font-bold mb-6">تقويم الفعاليات</h1>
      <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
        {events.map(ev=>(
          <div key={ev.id} className="p-4 bg-blue-50 rounded-xl text-center">
            <div className="font-bold">{ev.title}</div>
            <div className="text-gray-500">{ev.date_start}</div>
          </div>
        ))}
      </div>
    </div>
  );
}
