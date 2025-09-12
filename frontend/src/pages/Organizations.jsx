import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
export default function Organizations() {
  const [orgs, setOrgs] = useState([]);
  useEffect(()=>{ fetch("/api/organizations").then(r=>r.json()).then(setOrgs); },[]);
  return (
    <div className="max-w-5xl mx-auto mt-12">
      <h1 className="text-3xl font-bold mb-8">الجهات المشاركة</h1>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        {orgs.map(o=>(
          <Link to={`/organizations/${o.id}`} key={o.id} className="bg-white rounded-2xl p-6 shadow hover:scale-105 duration-100 flex flex-col items-center">
            <img src={o.logo || "/img/org_placeholder.png"} alt="" className="h-20 mb-4"/>
            <div className="font-bold text-xl mb-2">{o.name}</div>
            <div className="text-gray-500">{o.city}</div>
          </Link>
        ))}
      </div>
    </div>
  );
}
