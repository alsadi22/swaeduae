import React, { useEffect, useState } from "react";
export default function Certificates() {
  const [certs, setCerts] = useState([]);
  useEffect(()=>{ fetch("/api/my/certificates").then(r=>r.json()).then(setCerts); },[]);
  return (
    <div className="max-w-4xl mx-auto mt-10">
      <h1 className="text-3xl font-bold mb-8">شهاداتي التطوعية</h1>
      {certs.length === 0 && <div className="text-gray-600">لا توجد شهادات حتى الآن.</div>}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {certs.map(cert=>(
          <div key={cert.id} className="bg-white p-6 rounded-2xl shadow space-y-2">
            <div className="font-bold">{cert.event_title}</div>
            <div className="text-gray-500 text-sm">{cert.issue_date}</div>
            <a href={cert.pdf_url} target="_blank" className="text-blue-600 underline">تحميل الشهادة (PDF)</a>
            <div>رمز التحقق: <span className="font-mono text-xs">{cert.code}</span></div>
          </div>
        ))}
      </div>
    </div>
  );
}
