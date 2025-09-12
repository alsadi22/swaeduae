import React from "react";
import Header from "../components/Header";
import Footer from "../components/Footer";
export default function About() {
  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <div className="max-w-3xl mx-auto mt-12 bg-white p-8 rounded-2xl shadow-xl">
        <h1 className="text-3xl font-bold mb-4 text-blue-900">عن المنصة</h1>
        <p>
          منصة سواعد الإمارات هي منصة رائدة تهدف إلى تنظيم وتسهيل العمل التطوعي في دولة الإمارات العربية المتحدة، وتقديم فرص تطوعية لجميع أفراد المجتمع لدعم المبادرات الإنسانية والاجتماعية والثقافية والرياضية.
        </p>
        <p className="mt-2">
          نحن نسعى لأن نكون الوجهة الأولى للمتطوعين والجهات الراغبة في العمل التطوعي، من خلال واجهة سهلة الاستخدام وإدارة فعّالة للفرص والأعضاء والشهادات.
        </p>
      </div>
      <Footer />
    </div>
  );
}
