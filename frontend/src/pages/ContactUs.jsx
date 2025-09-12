import React from "react";
export default function ContactUs() {
  return (
    <div className="max-w-xl mx-auto py-12 px-6">
      <h2 className="text-2xl font-bold mb-6">تواصل معنا</h2>
      <form className="bg-white rounded-xl shadow p-6 flex flex-col gap-4">
        <input className="p-2 border rounded" placeholder="اسمك" />
        <input className="p-2 border rounded" placeholder="بريدك الإلكتروني" />
        <textarea className="p-2 border rounded" rows={5} placeholder="رسالتك"></textarea>
        <button className="bg-blue-700 text-white py-2 rounded">إرسال</button>
      </form>
    </div>
  );
}
