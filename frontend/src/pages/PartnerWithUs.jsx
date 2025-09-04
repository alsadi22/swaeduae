import React, { useState } from "react";
export default function PartnerWithUs() {
  const [sent, setSent] = useState(false);
  const handleSubmit = e => {
    e.preventDefault(); setSent(true); setTimeout(()=>setSent(false),2000);
  };
  return (
    <div className="max-w-2xl mx-auto py-10 px-4">
      <h2 className="text-2xl font-bold mb-6">انضم كشريك مؤسسي</h2>
      <form onSubmit={handleSubmit} className="bg-white rounded-xl shadow p-6 flex flex-col gap-4">
        <input required type="text" placeholder="اسم الجهة" className="border rounded px-4 py-2 text-right"/>
        <input required type="email" placeholder="البريد الإلكتروني" className="border rounded px-4 py-2 text-right"/>
        <input required type="text" placeholder="المدينة" className="border rounded px-4 py-2 text-right"/>
        <textarea placeholder="رسالة أو تفاصيل إضافية..." className="border rounded px-4 py-2 text-right"/>
        <button className="bg-green-700 text-white py-2 rounded">إرسال طلب الشراكة</button>
        {sent && <div className="text-green-700">تم إرسال الطلب بنجاح!</div>}
      </form>
    </div>
  );
}
