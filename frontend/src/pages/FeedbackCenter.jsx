import React, { useState } from "react";
export default function FeedbackCenter() {
  const [fb, setFb] = useState("");
  const [sent, setSent] = useState(false);
  const sendFeedback = () => {
    setSent(true);
    setTimeout(()=>setSent(false), 2000);
    setFb("");
  };
  return (
    <div className="max-w-lg mx-auto py-10 px-4">
      <h2 className="text-2xl font-bold mb-6">مركز التقييم والاقتراحات</h2>
      <textarea value={fb} onChange={e=>setFb(e.target.value)} placeholder="اكتب ملاحظتك أو اقتراحك..."
        className="border rounded px-4 py-2 w-full text-right mb-3" rows={4}/>
      <button onClick={sendFeedback} className="bg-blue-600 text-white py-2 px-8 rounded font-bold">إرسال</button>
      {sent && <div className="text-green-600 mt-4">شكراً لملاحظتك!</div>}
    </div>
  );
}
