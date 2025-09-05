import React, { useEffect, useState } from "react";
export default function Notifications() {
  const [notifs, setNotifs] = useState([]);
  useEffect(()=>{ fetch("/api/my/notifications").then(r=>r.json()).then(setNotifs); },[]);
  return (
    <div className="fixed top-20 right-4 z-40 bg-white rounded-2xl shadow-xl w-80 p-4">
      <div className="font-bold mb-2">الإشعارات</div>
      <ul className="divide-y">
        {notifs.length === 0 ? <li className="text-gray-400">لا توجد إشعارات.</li> :
          notifs.map(n=><li key={n.id} className="py-2">{n.text}</li>)
        }
      </ul>
    </div>
  );
}
