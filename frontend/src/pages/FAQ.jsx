import React from "react";
import Header from "../components/Header";
import Footer from "../components/Footer";
export default function FAQ() {
  const faqs = [
    { q: "كيف أسجل كمتطوع؟", a: "يمكنك التسجيل بسهولة من خلال الضغط على زر التسجيل وإدخال بياناتك الأساسية." },
    { q: "كيف أحصل على شهادة تطوع؟", a: "بعد المشاركة في الفعاليات المعتمدة سيتم إصدار الشهادة تلقائياً من لوحة التحكم." },
    { q: "هل المنصة معتمدة حكومياً؟", a: "نعم، بإشراف وزارة تنمية المجتمع الإماراتية." }
  ];
  return (
    <div dir="rtl">
      <Header />
      <main className="max-w-3xl mx-auto py-12 px-4">
        <h1 className="text-2xl font-bold mb-6 text-blue-900">الأسئلة الشائعة</h1>
        <ul className="space-y-6">
          {faqs.map((f, i) => (
            <li key={i} className="border rounded-lg shadow p-5 bg-white">
              <p className="font-bold text-lg mb-2 text-blue-800">{f.q}</p>
              <p className="text-gray-700">{f.a}</p>
            </li>
          ))}
        </ul>
      </main>
      <Footer />
    </div>
  );
}
