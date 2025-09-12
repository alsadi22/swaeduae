import React, { useState } from "react";
export default function WhatsAppCenter() {
  const [phone, setPhone] = useState("");
  const [msg, setMsg] = useState("");
  const [status, setStatus] = useState("");
  const sendWhatsApp = () => {
    // In reality, you'd call backend API to send WhatsApp message
    setStatus("✅ تم إرسال الإشعار إلى واتساب!");
    setTimeout(() => setStatus(""), 2500);
  };
  return (
    <div className="max-w-xl mx-auto py-10 px-4">
      <h2 className="text-2xl font-bold mb-6">مركز إشعارات واتساب</h2>
      <div className="bg-white rounded-xl shadow p-6 flex flex-col gap-3">
        <input type="text" value={phone} onChange={e=>setPhone(e.target.value)}
          placeholder="رقم الجوال مع كود الدولة (مثال: +971...)" className="border rounded px-4 py-2 text-right"/>
        <textarea value={msg} onChange={e=>setMsg(e.target.value)} placeholder="محتوى الرسالة..." className="border rounded px-4 py-2 text-right"/>
        <button className="bg-green-600 text-white py-2 rounded" onClick={sendWhatsApp}>إرسال إشعار واتساب</button>
        {status && <div className="text-green-700">{status}</div>}
      </div>
    </div>
  );
}
