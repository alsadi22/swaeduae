import React, { useEffect, useState } from "react";
export default function Resources() {
  const [resources, setResources] = useState([]);
  useEffect(()=>{ fetch("/api/resources").then(r=>r.json()).then(setResources); }, []);
  return (
    <div className="max-w-4xl mx-auto mt-10">
      <h1 className="text-3xl font-bold mb-6">مركز الموارد</h1>
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {resources.map(res=>(
          <a key={res.id} href={res.link} target="_blank" rel="noopener noreferrer"
             className="bg-white rounded-2xl p-6 shadow flex flex-col gap-3 hover:bg-blue-50">
            <div className="font-bold text-lg">{res.title}</div>
            <div className="text-gray-500">{res.type}</div>
            <div className="text-sm">{res.description}</div>
          </a>
        ))}
      </div>
    </div>
  );
}
