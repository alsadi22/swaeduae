import React, { useEffect, useState } from "react";
export default function Badges() {
  const [badges, setBadges] = useState([]);
  useEffect(()=>{ fetch("/api/my/badges").then(r=>r.json()).then(setBadges); },[]);
  return (
    <div className="max-w-3xl mx-auto mt-10">
      <h1 className="text-2xl font-bold mb-6">إنجازاتي وشاراتي</h1>
      <div className="flex gap-4 flex-wrap">
        {badges.map(b=>(
          <div key={b.id} className="bg-white rounded-xl p-4 shadow flex flex-col items-center min-w-[120px]">
            <img src={b.image || "/img/badge.png"} className="h-12 mb-2"/>
            <div className="font-bold">{b.title}</div>
            <div className="text-gray-500 text-xs">{b.hours} ساعة</div>
          </div>
        ))}
      </div>
    </div>
  );
}
