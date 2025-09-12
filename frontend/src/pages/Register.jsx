import React, { useState } from "react";
export default function Register() {
  const [form, setForm] = useState({ name: "", email: "", password: "" });
  const [ok, setOk] = useState(false);
  const submit = e => {
    e.preventDefault();
    fetch("/api/register", { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(form) })
      .then(r => r.ok ? setOk(true) : alert("حدث خطأ!"));
  };
  if (ok) return <div className="mt-10 text-center text-green-700 font-bold">تم التسجيل بنجاح! تحقق بريدك الإلكتروني.</div>;
  return (
    <form className="max-w-md mx-auto mt-10 bg-white p-8 rounded-2xl shadow" onSubmit={submit}>
      <h1 className="text-2xl font-bold mb-6">تسجيل حساب جديد</h1>
      <input className="w-full mb-3 p-3 rounded-xl border" placeholder="الاسم الكامل" required value={form.name} onChange={e=>setForm({...form,name:e.target.value})} />
      <input className="w-full mb-3 p-3 rounded-xl border" type="email" placeholder="البريد الإلكتروني" required value={form.email} onChange={e=>setForm({...form,email:e.target.value})} />
      <input className="w-full mb-6 p-3 rounded-xl border" type="password" placeholder="كلمة المرور" required value={form.password} onChange={e=>setForm({...form,password:e.target.value})} />
      <button className="w-full bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700">تسجيل</button>
      <div className="mt-4 text-center">
        <a href="/login" className="text-blue-600 underline">لديك حساب؟ تسجيل الدخول</a>
      </div>
    </form>
  );
}
