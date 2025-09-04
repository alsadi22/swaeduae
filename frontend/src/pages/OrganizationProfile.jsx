import React, { useEffect, useState } from "react";
import { useParams, Link } from "react-router-dom";
export default function OrganizationProfile() {
  const { id } = useParams();
  const [org, setOrg] = useState(null);
  useEffect(()=>{ fetch(`/api/organizations/${id}`).then(r=>r.json()).then(setOrg); },[id]);
  if (!org) return <div className="mt-20 text-center">جاري التحميل...</div>;
  return (
    <div className="max-w-3xl mx-auto mt-10 bg-white p-8 rounded-2xl shadow-xl">
      <div className="flex flex-col md:flex-row items-center mb-6 gap-6">
        <img src={org.logo || "/img/org_placeholder.png"} className="h-32 rounded-2xl"/>
        <div>
          <h1 className="text-2xl font-bold">{org.name}</h1>
          <div className="text-gray-500">{org.city}</div>
          <div className="mt-2">{org.description}</div>
        </div>
      </div>
      <h2 className="text-xl font-bold mt-8 mb-4">الفرص المتاحة</h2>
      <div className="space-y-4">
        {(org.events||[]).map(e=>(
          <Link to={`/events/${e.id}`} key={e.id} className="block bg-gray-100 rounded-xl p-4 hover:bg-blue-50">{e.title} ({e.date_start})</Link>
        ))}
      </div>
    </div>
  );
}
