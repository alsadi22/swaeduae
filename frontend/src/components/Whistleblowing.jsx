import React, { useState } from "react";
export default function Whistleblowing() {
  const [text, setText] = useState("");
  const [ok, setOk] = useState(false);
  const handleSubmit = e => { e.preventDefault(); fetch(`/api/whistleblowing`, {method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({text})}).then(()=>setOk(true)); };
  if (ok) return <div className="text-green-700 font-bold mt-10 text-center">تم استلام البلاغ بسرية تامة!</div>;
  return (
    <form className="max-w-lg mx-auto mt-12 bg-white p-8 rounded-2xl shadow" onSubmit={handleSubmit}>
      <h1 className="text-2xl font-bold mb-4">بلاغ/ملاحظة سرية</h1>
      <textarea className="w-full border rounded-xl p-2 mb-4" value={text} onChange={e=>setText(e.target.value)} placeholder="اكتب البلاغ أو الشكوى..." rows={4} required />
      <button className="bg-blue-700 px-5 py-2 text-white rounded-full">إرسال</button>
    </form>
  );
}
