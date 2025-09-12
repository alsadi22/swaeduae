import React, { useState } from "react";
export default function EventFeedback({eventId}) {
  const [text, setText] = useState("");
  const [ok, setOk] = useState(false);
  const handleSubmit = e => { e.preventDefault(); fetch(`/api/events/${eventId}/feedback`, {method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({text})}).then(()=>setOk(true)); };
  if (ok) return <div className="text-green-700 font-bold">شكرًا لتعليقك!</div>;
  return (
    <form onSubmit={handleSubmit} className="space-y-2">
      <textarea className="w-full border rounded-xl p-2" value={text} onChange={e=>setText(e.target.value)} placeholder="اكتب ملاحظاتك أو تقييمك..." rows={3} required />
      <button className="bg-blue-600 px-4 py-2 text-white rounded-full hover:bg-blue-700">إرسال</button>
    </form>
  );
}
