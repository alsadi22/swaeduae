import React from "react";
export default function AppDownload() {
  return (
    <section className="py-4 flex flex-col md:flex-row gap-4 justify-center items-center mt-4">
      <a href="#" className="bg-black text-white px-5 py-2 rounded flex items-center gap-2 font-bold hover:bg-gray-800">
        <i className="fab fa-apple text-2xl"></i> حمل الملف على متجر التطبيقات
      </a>
      <a href="#" className="bg-white border border-black px-5 py-2 rounded flex items-center gap-2 font-bold hover:bg-gray-100">
        <i className="fab fa-google-play text-2xl"></i> احصل عليه على جوجل بلاي
      </a>
    </section>
  );
}
