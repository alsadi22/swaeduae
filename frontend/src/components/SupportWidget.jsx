import React, { useState } from "react";
import { FaComment } from "react-icons/fa";
export default function SupportWidget() {
  const [open, setOpen] = useState(false);
  return (
    <div className="fixed bottom-20 right-6 z-50">
      <button className="bg-blue-700 text-white rounded-full p-4 shadow-lg" onClick={()=>setOpen(o=>!o)}>
        <FaComment size={28} />
      </button>
      {open && (
        <div className="mt-2 w-72 bg-white rounded-xl p-4 shadow-2xl">
          <div className="font-bold mb-2">دعم فني مباشر</div>
          <div className="text-sm mb-3">راسلنا وسيتم الرد عليك خلال دقائق.</div>
          <textarea className="w-full border rounded-xl p-2 mb-2" rows={2} placeholder="اكتب رسالتك..."></textarea>
          <button className="bg-blue-600 text-white px-4 py-2 rounded-full w-full">إرسال</button>
        </div>
      )}
    </div>
  );
}
