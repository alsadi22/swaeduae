import React, { useEffect, useState } from "react";
export default function MyApplications() {
  const [apps, setApps] = useState([]);
  useEffect(()=>{ fetch("/api/my/applications").then(r=>r.json()).then(setApps); },[]);
  return (
    <div className="max-w-4xl mx-auto mt-10">
      <h1 className="text-2xl font-bold mb-6">طلبات التطوع الخاصة بي</h1>
      <div className="space-y-4">
        {apps.length === 0 ? <div className="text-gray-500">لا توجد طلبات بعد.</div> :
          apps.map(app=>(
            <div key={app.id} className="bg-white p-4 rounded-xl shadow flex justify-between items-center">
              <div>{app.event_title}</div>
              <div>{app.status}</div>
            </div>
          ))}
      </div>
    </div>
  );
}
