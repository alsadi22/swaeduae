import React, { useState } from "react";
export default function RegisterOrg() {
  const [form, setForm] = useState({ name: "", email: "", phone: "", docs: "" });
  const [sent, setSent] = useState(false);
  function handleChange(e) {
    setForm(f => ({ ...f, [e.target.name]: e.target.value }));
  }
  function handleSubmit(e) {
    e.preventDefault();
    setSent(true);
    // TODO: Send org/KYC to backend
  }
  return (
    <div className="max-w-lg mx-auto mt-8 bg-white p-8 shadow rounded-xl">
      <h2 className="text-3xl font-bold mb-4">تسجيل جهة منظمة</h2>
      {sent ? (
        <div className="text-green-600">تم إرسال الطلب، سنراجع بياناتكم ونتواصل معكم قريبًا.</div>
      ) : (
        <form onSubmit={handleSubmit} className="space-y-4">
          <input name="name" required placeholder="اسم الجهة" className="w-full border rounded-lg p-2" value={form.name} onChange={handleChange} />
          <input name="email" type="email" required placeholder="البريد الإلكتروني" className="w-full border rounded-lg p-2" value={form.email} onChange={handleChange} />
          <input name="phone" required placeholder="رقم الهاتف" className="w-full border rounded-lg p-2" value={form.phone} onChange={handleChange} />
          <input name="docs" required placeholder="رابط مستندات KYC/تراخيص" className="w-full border rounded-lg p-2" value={form.docs} onChange={handleChange} />
          <button className="px-5 py-2 bg-green-600 text-white rounded-lg font-bold">تسجيل الجهة</button>
        </form>
      )}
    </div>
  );
}
