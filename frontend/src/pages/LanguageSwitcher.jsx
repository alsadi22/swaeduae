import React from "react";
export default function LanguageSwitcher({ lang, setLang }) {
  return (
    <div className="flex gap-2 items-center">
      <button onClick={() => setLang("ar")} className={`py-1 px-3 rounded ${lang==="ar" ? "bg-blue-700 text-white" : "bg-white border"}`}>العربية</button>
      <button onClick={() => setLang("en")} className={`py-1 px-3 rounded ${lang==="en" ? "bg-blue-700 text-white" : "bg-white border"}`}>EN</button>
    </div>
  );
}
